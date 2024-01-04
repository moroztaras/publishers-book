<?php

namespace App\Manager;

use App\Entity\Book;
use App\Manager\Recommendation\Model\RecommendationItem;
use App\Manager\Recommendation\RecommendationApiManager;
use App\Model\RecommendedBook;
use App\Model\RecommendedBookListResponse;
use App\Repository\BookRepository;

class RecommendationManager
{
    private const MAX_DESCRIPTION_LENGTH = 150;

    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly RecommendationApiManager $recommendationApiManager
    ) {
    }

    public function getRecommendationsByBookId(int $id): RecommendedBookListResponse
    {
        $ids = array_map(
            fn (RecommendationItem $item) => $item->getId(),
            $this->recommendationApiManager->getRecommendationsByBookId($id)->getRecommendations()
        );

        // Remap list books in model
        return new RecommendedBookListResponse(
            array_map($this->map(...), $this->bookRepository->findBooksByIds($ids))
        );
    }

    private function map(Book $book): RecommendedBook
    {
        $description = $book->getDescription();
        $description = strlen($description) > self::MAX_DESCRIPTION_LENGTH
            ? substr($description, 0, self::MAX_DESCRIPTION_LENGTH - 3).'...'
            : $description;

        return (new RecommendedBook())
            ->setId($book->getId())
            ->setImage($book->getImage())
            ->setSlug($book->getSlug())
            ->setTitle($book->getTitle())
            ->setShortDescription($description);
    }
}
