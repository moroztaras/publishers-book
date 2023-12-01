<?php

namespace App\Manager;

use App\Entity\Book;
use App\Exception\BookAlreadyExistsException;
use App\Exception\BookCoverNotFoundException;
use App\Model\Author\BookListItem;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\IdResponse;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private BookRepository $bookRepository,
        private SluggerInterface $slugger,
        private UploadFileManager $uploadFileManager,
    ) {
    }

    public function getBooks(UserInterface $user): BookListResponse
    {
        return new BookListResponse(
            array_map(
                [$this, 'map'],
                $this->bookRepository->findUserBooks($user)
            )
        );
    }

    public function createBook(CreateBookRequest $request, UserInterface $user): IdResponse
    {
        $slug = $this->slugger->slug($request->getTitle());
        if ($this->bookRepository->existsBySlug($slug)) {
            throw new BookAlreadyExistsException();
        }

        $book = (new Book())
            ->setTitle($request->getTitle())
            ->setMeap(false)
            ->setSlug($slug)
            ->setUser($user)
        ;

        $this->saveBook($book);

        return new IdResponse($book->getId());
    }

    public function uploadCover(int $id, UploadedFile $file): UploadCoverResponse
    {
        $book = $this->bookRepository->getBookById($id);
        $oldImage = $book->getImage();
        $link = $this->uploadFileManager->uploadBookFile($id, $file);

        $book->setImage($link);

        $this->em->flush();

        // Check & remove old file book cover
        if (null !== $oldImage) {
            $this->uploadFileManager->deleteBookFile($book->getId(), basename($oldImage));
        }

        return new UploadCoverResponse($link);
    }

    public function removeCover(int $id)
    {
        $book = $this->bookRepository->getBookById($id);
        $image = $book->getImage();

        if (null === $image) {
            throw new BookCoverNotFoundException();
        }

        $book->setImage(null);

        $this->em->flush();

        $this->uploadFileManager->deleteBookFile($id, basename($image));

        return null;
    }

    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getBookById($id);

        $this->em->remove($book);
        $this->em->flush();
    }

    private function saveBook(Book $book): void
    {
        $this->em->persist($book);
        $this->em->flush();
    }

    // Remap the books from the repository to the model
    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setTitle($book->getTitle())
        ;
    }
}
