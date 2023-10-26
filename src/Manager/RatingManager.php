<?php

namespace App\Manager;

use App\Repository\ReviewRepository;

class RatingManager implements RatingManagerInterface
{
    public function __construct(private ReviewRepository $reviewRepository)
    {
    }

    public function calcReviewRatingForBook(int $id, int $total): float
    {
        return $total > 0 ? $this->reviewRepository->getBookTotalRatingSum($id) / $total : 0;
    }
}
