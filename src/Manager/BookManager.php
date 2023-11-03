<?php

namespace App\Manager;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
use App\Manager\Recommendation\Model\RecommendationItem;
use App\Manager\Recommendation\RecommendationManager;
use App\Mapper\BookMapper;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\Collection;
use Exception;
use Psr\Log\LoggerInterface;

class BookManager implements BookManagerInterface
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookCategoryRepository $bookCategoryRepository,
        private ReviewRepository $reviewRepository,
        private RatingManager $ratingManager,
        private RecommendationManager $recommendationManager,
        private LoggerInterface $logger)
    {
    }

    // Receiving books in the specified category.
    public function getBooksByCategory(int $categoryId): BookListResponse
    {
        if (!$this->bookCategoryRepository->existsById($categoryId)) {
            throw new BookCategoryNotFoundException();
        }

        // Remap the books from the repository to the model
        return new BookListResponse(array_map(
            fn (Book $book) => BookMapper::map($book, new BookListItem()),
            $this->bookRepository->findBooksByCategoryId($categoryId)
        ));
    }

    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepository->getById($id);
        $reviews = $this->reviewRepository->countByBookId($id);
        $recommendations = [];

        // Remap the categories from field of book categories to the model
        $categories = $book->getCategories()
            ->map(fn (BookCategory $bookCategory) => new BookCategoryModel(
                $bookCategory->getId(),
                $bookCategory->getTitle(),
                $bookCategory->getSlug()
            ));

        try {
            $recommendations = $this->getRecommendations($id);
        } catch (Exception $ex) {
            $this->logger->error('Error while fetching recommendations', [
                'exception' => $ex->getMessage(),
                'bookId' => $id,
            ]);
        }

        return BookMapper::map($book, new BookDetails())
            ->setRating($this->ratingManager->calcReviewRatingForBook($id, $reviews))
            ->setReviews($reviews)
            ->setRecommendations($recommendations)
            ->setFormats($this->mapFormats($book->getFormats()))
            ->setCategories($categories->toArray())
        ;
    }

    /**
     * @param Collection<BookToBookFormat> $formats
     */
    private function mapFormats(Collection $formats): array
    {
        return $formats->map(fn (BookToBookFormat $formatJoin) => (new BookFormat())
            ->setId($formatJoin->getFormat()->getId())
            ->setTitle($formatJoin->getFormat()->getTitle())
            ->setDescription($formatJoin->getFormat()->getDescription())
            ->setComment($formatJoin->getFormat()->getComment())
            ->setPrice($formatJoin->getPrice())
            ->setDiscountPercent(
                $formatJoin->getDiscountPercent()
            ))->toArray();
    }

    private function getRecommendations(int $bookId): array
    {
        $ids = array_map(
            fn (RecommendationItem $item) => $item->getId(),
            $this->recommendationManager->getRecommendationsByBookId($bookId)->getRecommendations()
        );

        return array_map([BookMapper::class, 'mapRecommended'], $this->bookRepository->findBooksByIds($ids));
    }
}
