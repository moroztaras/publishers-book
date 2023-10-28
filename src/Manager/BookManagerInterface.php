<?php

namespace App\Manager;

use App\Model\BookDetails;
use App\Model\BookListResponse;

interface BookManagerInterface
{
    public function getBooksByCategory(int $categoryId): BookListResponse;

    public function getBookById(int $id): BookDetails;
}
