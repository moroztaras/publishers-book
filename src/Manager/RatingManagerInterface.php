<?php

namespace App\Manager;

interface RatingManagerInterface
{
    public function calcReviewRatingForBook(int $id, int $total): float;
}
