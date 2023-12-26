<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Exception\BookChapterInvalidSortException;
use App\Manager\AuthorBookChapterManager;
use App\Manager\BookChapterManager;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\IdResponse;
use App\Repository\BookChapterRepository;
use App\Repository\BookRepository;
use App\Tests\AbstractTestCase;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

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

    public function testCreateChapterNested(): void
    {
        $book = new Book();
        $parentBookChapter = (new BookChapter())->setLevel(1);

        $this->bookChapterRepository->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($parentBookChapter);

        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('test')
            ->willReturn(new UnicodeString('test'));

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookChapterRepository->expects($this->once())
            ->method('getMaxSort')
            ->with($book, 2)
            ->willReturn(5);

        $this->bookChapterRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($this->callback(function (BookChapter $chapter) use ($book, $parentBookChapter) {
                $expectedChapter = (new BookChapter())
                    ->setBook($book)
                    ->setSort(6)
                    ->setLevel(2)
                    ->setTitle('test')
                    ->setSlug('test')
                    ->setParent($parentBookChapter);

                $this->assertEquals($expectedChapter, $chapter);
                $this->setEntityId($chapter, 1);

                return true;
            }));

        // Create request
        $payload = (new CreateBookChapterRequest())->setTitle('test')->setParentId(2);

        // Comparing the expected value with the actual returned value
        $this->assertEquals(
            new IdResponse(1),
            $this->createManager()->createChapter($payload, 1),
        );
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
