<?php

namespace App\Manager;

use App\Entity\Book;
use App\Exception\BookAlreadyExistsException;
use App\Model\Author\BookListItem;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\IdResponse;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private BookRepository $bookRepository,
        private SluggerInterface $slugger,
        private Security $security
    ) {
    }

    public function getBooks(): BookListResponse
    {
        return new BookListResponse(
            array_map(
                [$this, 'map'],
                $this->bookRepository->findUserBooks($this->security->getUser())
            )
        );
    }

    public function createBook(CreateBookRequest $request): IdResponse
    {
        $slug = $this->slugger->slug($request->getTitle());
        if ($this->bookRepository->existsBySlug($slug)) {
            throw new BookAlreadyExistsException();
        }

        $book = (new Book())
            ->setTitle($request->getTitle())
            ->setMeap(false)
            ->setSlug($slug)
            ->setUser($this->security->getUser())
        ;

        $this->saveBook($book);

        return new IdResponse($book->getId());
    }

    private function saveBook(Book $book):void
    {
        $this->em->persist($book);
        $this->em->flush();
    }

    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getUserBookById($id, $this->security->getUser());

        $this->em->remove($book);
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
