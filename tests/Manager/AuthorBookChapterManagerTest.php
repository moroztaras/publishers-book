<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Exception\BookChapterInvalidSortException;
use App\Manager\AuthorBookChapterManager;
use App\Manager\BookChapterManager;
use App\Model\Author\CreateBookChapterRequest;
use App\Repository\BookChapterRepository;
use App\Repository\BookRepository;
use App\Tests\AbstractTestCase;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookChapterManagerTest extends AbstractTestCase
{
    private BookChapterRepository $bookChapterRepository;

    private BookRepository $bookRepository;

    private BookChapterManager $bookChapterManager;

    private SluggerInterface $slugger;

    protected function setUp(): void
    {
        $this->bookChapterRepository = $this->createMock(BookChapterRepository::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookChapterManager = $this->createMock(BookChapterManager::class);
        $this->slugger = $this->createMock(SluggerInterface::class);

        parent::setUp();
    }

    public function testCreateChapterMaxLevelException(): void
    {
        // Expect exception
        $this->expectException(BookChapterInvalidSortException::class);
        // Create book
        $book = new Book();
        // Create chapter
        $parentBookChapter = (new BookChapter())->setLevel(3);

        // Set the behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        // Set the behavior and return result for method - getById
        $this->bookChapterRepository->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($parentBookChapter);
        // Request
        $payload = (new CreateBookChapterRequest())->setTitle('test')->setParentId(2);

        // Run Chapter manager
        $this->createManager()->createChapter($payload, 1);
    }

    // Create AuthorBookChapterManager
    private function createManager(): AuthorBookChapterManager
    {
        return new AuthorBookChapterManager(
            $this->bookRepository,
            $this->bookChapterRepository,
            $this->bookChapterManager,
            $this->slugger,
        );
    }
}
