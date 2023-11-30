<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
use App\Manager\BookManager;
use App\Manager\Rating;
use App\Manager\RatingManager;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat as BookFormatModel;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

// Unit teats
class BookManagerTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    private BookCategoryRepository $bookCategoryRepository;

    private RatingManager $ratingManager;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
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
            ->method('findPublishedBooksByCategoryId')
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

    //  Testing book by id
    public function testGetBookById(): void
    {
        // Set the expectation from the method - getPublishedById
        $this->bookRepository->expects($this->once())
            ->method('getPublishedById')
            ->with(123)
            ->willReturn($this->createBookEntity());

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
            ->setTitle('Test Book')
            ->setImage('http://localhost/test.png')
            ->setAuthors(['Tester'])
            ->setMeap(false)
            ->setCategories([
                new BookCategoryModel(1, 'Category', 'category'),
            ])
            ->setPublicationDate(1602288000)
            ->setFormats([$format]);

        $this->assertEquals($expected, $this->createBookManager()->getBookById(123));
    }

    // Create Book Manager
    private function createBookManager(): BookManager
    {
        return new BookManager(
            $this->bookRepository,
            $this->bookCategoryRepository,
            $this->ratingManager
        );
    }

    // Create new Book entity
    private function createBookEntity(): Book
    {
        // Create category
        $category = (new BookCategory())->setTitle('Category')->setSlug('category');
        // Set id for category
        $this->setEntityId($category, 1);

        // Create format
        $format = (new BookFormat())->setTitle('format')->setDescription('description format')->setComment(null);
        // Set id for format
        $this->setEntityId($format, 1);

        // Create join to BookToBookFormat
        $join = (new BookToBookFormat())
            ->setPrice(123.55)
            ->setFormat($format)
            ->setDiscountPercent(5);
        // Set id for BookToBookFormat
        $this->setEntityId($join, 1);

        $book = (new Book())
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setIsbn('123321')
            ->setDescription('Test description')
            ->setAuthors(['Tester'])
            ->setImage('http://localhost/test.png')
            ->setCategories(new ArrayCollection([$category]))
            ->setPublicationDate(new \DateTimeImmutable('2020-10-10'))
            ->setFormats(new ArrayCollection([$join]))
        ;

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
