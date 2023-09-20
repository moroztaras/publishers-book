<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Exception\BookCategoryNotFoundException;
use App\Manager\BookManager;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

// Unit teats
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

    public function testGetBooksByCategory(): void
    {
        // Mock for BookRepository
        $bookRepository = $this->createMock(BookRepository::class);
        // Set behavior for BookRepository
        $bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        // Mock for BookCategoryRepository
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        // Set behavior for BookCategoryRepository
        $bookCategoryRepository->expects($this->once())
            ->method('getById')
            ->with(130)
            ->willReturn(new BookCategory());

        // Create manager
        $manager = new BookManager($bookRepository, $bookCategoryRepository);

        // Expected value
        $expected = new BookListResponse([$this->createBookItemModel()]);

        // Comparing the actual returned value with the expected value.
        $this->assertEquals($expected, $manager->getBooksByCategory(130));
    }

    // Create new Book entity
    private function createBookEntity(): Book
    {
        return (new Book())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('http://localhost/test.png')
            ->setCategories(new ArrayCollection())
            ->setPublicationDate(new \DateTime('2020-10-10'));
    }

    // Create BookListItem
    private function createBookItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setImage('http://localhost/test.png')
            ->setPublicationDate(1602288000);
    }
}
