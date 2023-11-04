<?php

namespace App\Manager;

use App\Repository\ReviewRepository;

class RatingManager implements RatingManagerInterface
{
    public function __construct(private ReviewRepository $reviewRepository)
    {
    }

    public function calcReviewRatingForBook(int $id): Rating
    {
        $total = $this->reviewRepository->countByBookId($id);
        $rating = $total > 0 ? $this->reviewRepository->getBookTotalRatingSum($id) / $total : 0;

        return new Rating($total, $rating);
    }
}
