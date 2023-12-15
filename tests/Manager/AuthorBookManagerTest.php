<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Entity\User;
use App\Exception\BookAlreadyExistsException;
use App\Manager\AuthorBookManager;
use App\Manager\UploadFileManager;
use App\Model\Author\BookListItem;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\UpdateBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\IdResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookFormatRepository;
use App\Repository\BookRepository;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\UnicodeString;

class AuthorBookManagerTest extends AbstractTestCase
{
    const LINK_IMAGE_FILE = 'http://localhost/new.jpg';

    private BookRepository $bookRepository;

    private BookFormatRepository $bookFormatRepository;

    private BookCategoryRepository $bookCategoryRepository;

    private SluggerInterface $slugger;

    private UploadFileManager $uploadFileManager;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookFormatRepository = $this->createMock(BookFormatRepository::class);
        $this->bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->uploadFileManager = $this->createMock(UploadFileManager::class);
    }

    // Test on upload file for cover of book
    public function testUploadCover(): void
    {
        $file = new UploadedFile('path', 'field', null, UPLOAD_ERR_NO_FILE, true);
        // Create book
        $book = (new Book())->setImage(null);
        $this->setEntityId($book, 1);

        // Set the behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        // Set the behavior for method - commit
        $this->bookRepository->expects($this->once())
            ->method('commit');

        // Set the behavior and return result for method - uploadBookFile
        $this->uploadFileManager->expects($this->once())
            ->method('uploadBookFile')
            ->with(1, $file)
            ->willReturn(self::LINK_IMAGE_FILE);

        // Expected value
        $expected = new UploadCoverResponse(self::LINK_IMAGE_FILE);

        // Comparing the expected value with the actual returned value
        $this->assertEquals($expected, $this->createManager()->uploadCover(1, $file));
    }

    public function testUploadCoverRemoveOld(): void
    {
        $file = new UploadedFile('path', 'field', null, UPLOAD_ERR_NO_FILE, true);
        $book = (new Book())->setImage('http://localhost/old.png');
        $this->setEntityId($book, 1);

        // Set the behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        // Set the behavior for method - commit
        $this->bookRepository->expects($this->once())
            ->method('commit');

        // Set the behavior and return result for method - uploadBookFile
        $this->uploadFileManager->expects($this->once())
            ->method('uploadBookFile')
            ->with(1, $file)
            ->willReturn(self::LINK_IMAGE_FILE);

        // Set the behavior and return result for method - deleteBookFile
        $this->uploadFileManager->expects($this->once())
            ->method('deleteBookFile')
            ->with(1, 'old.png');

        // Expected value
        $expected = new UploadCoverResponse(self::LINK_IMAGE_FILE);

        // Comparing the expected value with the actual returned value
        $this->assertEquals($expected, $this->createManager()->uploadCover(1, $file));
    }

    public function testDeleteBook(): void
    {
        $book = new Book();

        // Set the behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        // Set the behavior and return result for method - removeAndCommit
        $this->bookRepository->expects($this->once())
            ->method('removeAndCommit')
            ->with($book);

        // Run method - deleteBook
        $this->createManager()->deleteBook(1);
    }

    public function testGetBook(): void
    {
        // Create category
        $category = MockUtils::createBookCategory();
        $this->setEntityId($category, 1);

        // Create format
        $format = MockUtils::createBookFormat();
        $this->setEntityId($format, 1);

        // Create book
        $book = MockUtils::createBook()->setCategories(new ArrayCollection([$category]));
        // Create book link format
        $bookLink = MockUtils::createBookFormatLink($book, $format);
        $book->setFormats(new ArrayCollection([$bookLink]));

        $this->setEntityId($book, 1);

        // Set the behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        // Comparing the expected value with the actual returned value
        $this->assertEquals(MockUtils::bookDetails(), $this->createManager()->getBook(1));
    }

    public function testGetBooks(): void
    {
        // Create user
        $user = new User();
        // Create book
        $book = MockUtils::createBook();
        $this->setEntityId($book, 1);

        // Set the behavior and return result for method - findUserBooks
        $this->bookRepository->expects($this->once())
            ->method('findUserBooks')
            ->with($user)
            ->willReturn([$book]);
        // Set value in BookListItem
        $bookItem = (new BookListItem())->setId(1)
            ->setImage('http://localhost.png')
            ->setTitle('Test book')
            ->setSlug('test-book');

        // Comparing the expected value with the actual returned value
        $this->assertEquals(new BookListResponse([$bookItem]), $this->createManager()->getBooks($user));
    }

    public function testCreateBook(): void
    {
        // Request
        $payload = (new CreateBookRequest())->setTitle('New Book');
        // New user
        $user = new User();

        // Create new book
        $expectedBook = (new Book())->setTitle('New Book')
            ->setSlug('new-book')
            ->setUser($user);

        // Set the behavior and return result for method - slug
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New Book')
            ->willReturn(new UnicodeString('new-book'));

        // Set the behavior and return result for method - existsBySlug
        $this->bookRepository->expects($this->once())
            ->method('existsBySlug')
            ->with('new-book')
            ->willReturn(false);

        // Set the behavior and return result for method - saveAndCommit
        $this->bookRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($expectedBook)
            ->will($this->returnCallback(function (Book $book) {
                $this->setEntityId($book, 111);
            }));

        // Comparing the expected value with the actual returned value
        $this->assertEquals(new IdResponse(111), $this->createManager()->createBook($payload, $user));
    }

    public function testCreateBookSlugExistsException(): void
    {
        // Expect exception
        $this->expectException(BookAlreadyExistsException::class);

        // Request
        $payload = (new CreateBookRequest())->setTitle('New Book');
        $user = new User();

        // Set the behavior and return result for method - slug
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('New Book')
            ->willReturn(new UnicodeString('new-book'));

        // Set the behavior and return result for method - existsBySlug
        $this->bookRepository->expects($this->once())
            ->method('existsBySlug')
            ->with('new-book')
            ->willReturn(true);

        // Comparing the expected value with the actual returned value
        $this->assertEquals(new IdResponse(111), $this->createManager()->createBook($payload, $user));
    }

    public function testUpdateBookExceptionOnDuplicateSlug(): void
    {
        // Expect exception
        $this->expectException(BookAlreadyExistsException::class);

        $book = new Book();
        // Request
        $payload = (new UpdateBookRequest())->setTitle('Old');

        // Set the behavior and return result for method - slug
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('Old')
            ->willReturn(new UnicodeString('old'));

        // Set the behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        // Set the behavior and return result for method - existsBySlug
        $this->bookRepository->expects($this->once())
            ->method('existsBySlug')
            ->with('old')
            ->willReturn(true);

        // Run method updateBook
        $this->createManager()->updateBook(1, $payload);
    }

    // Create AuthorBookManager
    private function createManager(): AuthorBookManager
    {
        return new AuthorBookManager(
            $this->bookRepository,
            $this->bookFormatRepository,
            $this->bookCategoryRepository,
            $this->slugger,
            $this->uploadFileManager,
        );
    }
}
