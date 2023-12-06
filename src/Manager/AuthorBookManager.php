<?php

namespace App\Manager;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Exception\BookAlreadyExistsException;
use App\Exception\BookCoverNotFoundException;
use App\Mapper\BookMapper;
use App\Model\Author\BookDetails;
use App\Model\Author\BookFormatOptions;
use App\Model\Author\BookListItem;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\UpdateBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\IdResponse;
use App\Repository\BookRepository;
use App\Repository\BookCategoryRepository;
use App\Repository\BookFormatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookManager
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookFormatRepository $bookFormatRepository,
        private BookCategoryRepository $bookCategoryRepository,
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

        $this->bookRepository->saveAndCommit($book);

        return new IdResponse($book->getId());
    }

    public function getBook(int $id): BookDetails
    {
        $book = $this->bookRepository->getBookById($id);

        $bookDetails = (new BookDetails())
            ->setIsbn($book->getIsbn())
            ->setDescription($book->getDescription())
            ->setFormats(BookMapper::mapFormats($book))
            ->setCategories(BookMapper::mapCategories($book));

        return BookMapper::map($book, $bookDetails);
    }

    public function uploadCover(int $id, UploadedFile $file): UploadCoverResponse
    {
        $book = $this->bookRepository->getBookById($id);
        $oldImage = $book->getImage();
        $link = $this->uploadFileManager->uploadBookFile($id, $file);

        $book->setImage($link);

        $this->bookRepository->commit();

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

        $this->bookRepository->commit();

        $this->uploadFileManager->deleteBookFile($id, basename($image));

        return null;
    }

    public function updateBook(int $id, UpdateBookRequest $request): void
    {
        // Get book by id
        $book = $this->bookRepository->getBookById($id);
        $title = $request->getTitle();
        if (!empty($title)) {
            $book->setTitle($title)->setSlug($this->slugifyOfThrow($title));
        }

        $formats = array_map(function (BookFormatOptions $options) use ($book): BookToBookFormat {
            // The creation a new relationship from the book to the format
            $format = (new BookToBookFormat())
                ->setPrice($options->getPrice())
                ->setDiscountPercent($options->getDiscountPercent())
                ->setBook($book)
                ->setFormat($this->bookFormatRepository->getById($options->getId()));
            // Save reference
            $this->bookRepository->saveBookFormatReference($format);

            return $format;
        }, $request->getFormats());

        // Remove book old formats
        foreach ($book->getFormats() as $format) {
            $this->bookRepository->removeBookFormatReference($format);
        }

        $book->setAuthors($request->getAuthors())
            ->setIsbn($request->getIsbn())
            ->setDescription($request->getDescription())
            ->setCategories(new ArrayCollection(
                $this->bookCategoryRepository->findBookCategoriesByIds($request->getCategories())
            ))
            ->setFormats(new ArrayCollection($formats));

        $this->bookRepository->commit();
    }

    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getBookById($id);

        $this->bookRepository->removeAndCommit($book);
    }

    // Slug for book
    private function slugifyOfThrow(string $title): string
    {
        $slug = $this->slugger->slug($title);
        if ($this->bookRepository->existsBySlug($slug)) {
            throw new BookAlreadyExistsException();
        }

        return $slug;
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
