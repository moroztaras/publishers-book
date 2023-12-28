<?php

namespace App\Manager;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Mapper\BookMapper;
use App\Model\BookDetails;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;

class BookManager implements BookManagerInterface
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookChapterManager $bookChapterManager,
        private BookCategoryRepository $bookCategoryRepository,
        private RatingManager $ratingManager
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
            function (Book $book) {
                $item = new BookListItem();
                BookMapper::map($book, $item);

                return $item;
            },
            $this->bookRepository->findPublishedBooksByCategoryId($categoryId)
        ));
    }

    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepository->getPublishedById($id);
        $rating = $this->ratingManager->calcReviewRatingForBook($id);
        $details = new BookDetails();

        BookMapper::map($book, $details);

        return $details
            ->setRating($rating->getRating())
            ->setReviews($rating->getTotal())
            ->setFormats(BookMapper::mapFormats($book))
            ->setCategories(BookMapper::mapCategories($book))
            ->setChapters($this->bookChapterManager->getChaptersTree($book)->getItems());
    }
}
