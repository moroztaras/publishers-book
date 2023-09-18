<?php

namespace App\Manager;

use App\Model\BookCategoryListResponse;

interface BookCategoryManagerInterface
{
    public function getCategories(): BookCategoryListResponse;
}
