<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Manager\BookManager;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

// Unit teats
class BookManagerTest extends AbstractTestCase
{
    // Category Not Found
    public function testGetBooksByCategoryNotFound(): void
    {
        // Mock for ReviewRepository
        $reviewRepository = $this->createMock(ReviewRepository::class);
        // Mock for BookRepository
        $bookRepository = $this->createMock(BookRepository::class);
        // Mock for BookCategoryRepository
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        // Set behavior for BookCategoryRepository
        $bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false)
        ;

        // Expect exception
        $this->expectException(BookCategoryNotFoundException::class);

        // Run manager
        (new BookManager($bookRepository, $bookCategoryRepository, $reviewRepository))->getBooksByCategory(130);
    }

    public function testGetBooksByCategory(): void
    {
        // Mock for ReviewRepository
        $reviewRepository = $this->createMock(ReviewRepository::class);
        // Mock for BookRepository
        $bookRepository = $this->createMock(BookRepository::class);
        // Set behavior for BookRepository
        $bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()])
        ;

        // Mock for BookCategoryRepository
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        // Set behavior for BookCategoryRepository
        $bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true)
        ;

        // Create manager
        $manager = new BookManager($bookRepository, $bookCategoryRepository, $reviewRepository);

        // Expected value
        $expected = new BookListResponse([$this->createBookItemModel()]);

        // Comparing the actual returned value with the expected value.
        $this->assertEquals($expected, $manager->getBooksByCategory(130));
    }

    // Create new Book entity
    private function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setIsbn('123321')
            ->setDescription('Test description')
            ->setAuthors(['Tester'])
            ->setImage('http://localhost/test.png')
            ->setCategories(new ArrayCollection())
            ->setPublicationDate(new \DateTimeImmutable('2020-10-10'));

        $this->setEntityId($book, 123);

        return $book;
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
