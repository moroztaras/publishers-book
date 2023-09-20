<?php

namespace App\Manager;

use App\Model\BookListResponse;

interface BookManagerInterface
{
    public function getBooksByCategory(int $categoryId): BookListResponse;
}
