<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Manager\BookManager;
use App\Manager\RatingManager;
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
    private ReviewRepository $reviewRepository;

    private BookRepository $bookRepository;

    private BookCategoryRepository $bookCategoryRepository;

    private RatingManager $ratingManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $this->ratingManager = $this->createMock(RatingManager::class);
    }

    // Category Not Found
    public function testGetBooksByCategoryNotFound(): void
    {
        // Set behavior for BookCategoryRepository
        $this->bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false);

        // Expect exception
        $this->expectException(BookCategoryNotFoundException::class);

        // Run manager
        $this->createBookManager()->getBooksByCategory(130);
    }

    public function testGetBooksByCategory(): void
    {
        // Set behavior for BookRepository
        $this->bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()])
        ;

        // Set behavior for BookCategoryRepository
        $this->bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true)
        ;

        // Expected value
        $expected = new BookListResponse([$this->createBookItemModel()]);

        // Comparing the expected value with the actual returned value .
        $this->assertEquals($expected, $this->createBookManager()->getBooksByCategory(130));
    }

    // Create Book Manager
    private function createBookManager(): BookManager
    {
        return new BookManager(
            $this->bookRepository,
            $this->bookCategoryRepository,
            $this->reviewRepository,
            $this->ratingManager
        );
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
