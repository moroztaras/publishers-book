<?php

namespace App\Tests\Manager;

use App\Exception\BookCategoryNotFoundException;
use App\Manager\BookManager;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use PHPUnit\Framework\TestCase;

class BookManagerTest extends TestCase
{
    // Category Not Found
    public function testGetBooksByCategoryNotFound(): void
    {
        // Mock for BookRepository
        $bookRepository = $this->createMock(BookRepository::class);
        // Mock for BookCategoryRepository
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        // Set behavior for BookCategoryRepository
        $bookCategoryRepository->expects($this->once())
            ->method('getById')
            ->with(130)
            ->willThrowException(new BookCategoryNotFoundException());

        // Expect exception
        $this->expectException(BookCategoryNotFoundException::class);

        // Run manager
        (new BookManager($bookRepository, $bookCategoryRepository))->getBooksByCategory(130);
    }
}
