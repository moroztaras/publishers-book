<?php

namespace App\Manager;

use App\Model\BookCategoryListResponse;
use App\Model\BookCategoryUpdateRequest;
use App\Model\IdResponse;

interface BookCategoryManagerInterface
{
    public function createCategory(BookCategoryUpdateRequest $updateRequest): IdResponse;

    public function updateCategory(int $id, BookCategoryUpdateRequest $updateRequest): void;

    public function getCategories(): BookCategoryListResponse;

    public function deleteCategory(int $id): void;
}
