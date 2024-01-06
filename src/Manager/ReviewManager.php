<?php

namespace App\Manager;

use App\Entity\Review;
use App\Model\Review as ReviewModel;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;

class ReviewManager
{
    private const PAGE_LIMIT = 5;

    public function __construct(
        private readonly ReviewRepository $reviewRepository,
        private readonly RatingManager $ratingManager
    ) {
    }

    public function getReviewPageByBookId(int $id, int $page): ReviewPage
    {
        // Calculate offset
        $paginator = $this->reviewRepository->getPageByBookId(
            $id,
            PaginationUtils::calcOffset($page, self::PAGE_LIMIT),
            self::PAGE_LIMIT
        );
        $items = [];

        foreach ($paginator as $item) {
            $items[] = $this->map($item);
        }

        $rating = $this->ratingManager->calcReviewRatingForBook($id);
        $total = $rating->getTotal();

        return (new ReviewPage())
            ->setRating($rating->getRating())
            ->setTotal($total)
            ->setPage($page)
            ->setPerPage(self::PAGE_LIMIT)
            ->setPages(PaginationUtils::calcPages($total, self::PAGE_LIMIT))
            ->setItems($items);
    }

    // Remap the entity review to model
    public function map(Review $review): ReviewModel
    {
        return (new ReviewModel())
            ->setId($review->getId())
            ->setRating($review->getRating())
            ->setCreatedAt($review->getCreatedAt()->getTimestamp())
            ->setAuthor($review->getAuthor())
            ->setContent($review->getContent());
    }
}
