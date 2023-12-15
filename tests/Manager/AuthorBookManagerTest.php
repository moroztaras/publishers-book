<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Manager\AuthorBookManager;
use App\Manager\UploadFileManager;
use App\Model\Author\UploadCoverResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookFormatRepository;
use App\Repository\BookRepository;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
