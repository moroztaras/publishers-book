<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Manager\BookChapterManager;
use App\Manager\BookManager;
use App\Manager\Rating;
use App\Manager\RatingManager;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookChapterTreeResponse;
use App\Model\BookDetails;
use App\Model\BookFormat as BookFormatModel;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;

// Unit teats
class BookManagerTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    private BookChapterManager $bookChapterManager;

    private BookCategoryRepository $bookCategoryRepository;

    private RatingManager $ratingManager;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookChapterManager = $this->createMock(BookChapterManager::class);
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
            ->method('findPublishedBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        // Set behavior for BookCategoryRepository
        $this->bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true);

        // Expected value
        $expected = new BookListResponse([$this->createBookItemModel()]);

        // Comparing the expected value with the actual returned value .
        $this->assertEquals($expected, $this->createBookManager()->getBooksByCategory(130));
    }

    //  Testing book by id
    public function testGetBookById(): void
    {
        // Create book
        $book = $this->createBookEntity();

        // Set the expectation from the method - getChaptersTree
        $this->bookChapterManager->expects($this->once())
            ->method('getChaptersTree')
            ->with($book)
            ->willReturn(new BookChapterTreeResponse());

        // Set the expectation from the method - getPublishedById
        $this->bookRepository->expects($this->once())
            ->method('getPublishedById')
            ->with(123)
            ->willReturn($book);

        // Set the expectation from the method - calcReviewRatingForBook
        $this->ratingManager->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(123)
            ->willReturn(new Rating(10, 5.5));

        $format = (new BookFormatModel())
            ->setId(1)
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null)
            ->setPrice(123.55)
            ->setDiscountPercent(5);

        // Expected BookDetails model
        $expected = (new BookDetails())->setId(123)
            ->setRating(5.5)
            ->setReviews(10)
            ->setSlug('test-book')
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setAuthors(['Tester'])
            ->setCategories([
                new BookCategoryModel(1, 'Devices', 'devices'),
            ])
            ->setPublicationDate(1602288000)
            ->setFormats([$format])
            ->setChapters([]);

        $this->assertEquals($expected, $this->createBookManager()->getBookById(123));
    }

    // Create Book Manager
    private function createBookManager(): BookManager
    {
        return new BookManager(
            $this->bookRepository,
            $this->bookChapterManager,
            $this->bookCategoryRepository,
            $this->ratingManager
        );
    }

    // Create new Book entity
    private function createBookEntity(): Book
    {
        // Create category
        $category = MockUtils::createBookCategory();
        // Set id for category
        $this->setEntityId($category, 1);
        // Create format
        $format = MockUtils::createBookFormat();
        // Create book
        $book = MockUtils::createBook()->setCategories(new ArrayCollection([$category]));
        $this->setEntityId($book, 123);
        // Set id for format
        $this->setEntityId($format, 1);

        // Create join to BookToBookFormat
        $join = MockUtils::createBookFormatLink($book, $format);
        // Set id for BookToBookFormat
        $this->setEntityId($join, 1);

        $book->setFormats(new ArrayCollection([$join]));

        return $book;
    }

    // Create BookListItem
    private function createBookItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setAuthors(['Tester'])
            ->setImage('http://localhost.png')
            ->setPublicationDate(1602288000);
    }
}
