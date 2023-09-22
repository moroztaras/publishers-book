<?php

namespace App\Manager;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;

class BookManager implements BookManagerInterface
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookCategoryRepository $bookCategoryRepository
    ) {
    }

    // Receiving books in the specified category.
    public function getBooksByCategory(int $categoryId): BookListResponse
    {
        if (!$this->bookCategoryRepository->existsById($categoryId)) {
            throw new BookCategoryNotFoundException();
        }

        // Remap the books from the repository to the model
        return new BookListResponse(array_map(
            [$this, 'map'],
            $this->bookRepository->findBooksByCategoryId($categoryId)
        ));
    }

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationDate($book->getPublicationDate()->getTimestamp());
    }
}
