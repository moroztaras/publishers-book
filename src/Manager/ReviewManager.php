<?php

namespace App\Manager;

use App\Entity\Review;
use App\Model\Review as ReviewModel;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;

class ReviewManager
{
    private const PAGE_LIMIT = 5;

    public function __construct(private ReviewRepository $reviewRepository)
    {
    }

    public function getReviewPageByBookId(int $id, int $page): ReviewPage
    {
        // Calculate offset
        $offset = max($page - 1, 0) * self::PAGE_LIMIT;
        $paginator = $this->reviewRepository->getPageByBookId($id, $offset, self::PAGE_LIMIT);
        $total = count($paginator);
        $rating = 0;

        // Calculate rating for book
        if ($total > 0) {
            $rating = $this->reviewRepository->getBookTotalRatingSum($id) / $total;
        }

        return (new ReviewPage())
            ->setRating($rating)
            ->setTotal($total)
            ->setPage($page)
            ->setPerPage(self::PAGE_LIMIT)
            ->setPages(ceil($total / self::PAGE_LIMIT))
            ->setItems(array_map([$this, 'map'], $paginator->getIterator()->getArrayCopy()));
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
