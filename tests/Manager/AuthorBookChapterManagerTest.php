<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Exception\BookChapterInvalidSortException;
use App\Manager\AuthorBookChapterManager;
use App\Manager\BookChapterManager;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\Author\UpdateBookChapterRequest;
use App\Model\Author\UpdateBookChapterSortRequest;
use App\Model\BookChapterTreeResponse;
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

    public function testCreateChapter(): void
    {
        // Create book
        $book = new Book();

        // Set the behavior and return result for method - slug
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with('test')
            ->willReturn(new UnicodeString('test'));

        // Set the behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        // Set the behavior and return result for method - getMaxSort
        $this->bookChapterRepository->expects($this->once())
            ->method('getMaxSort')
            ->with($book, 1)
            ->willReturn(5);

        // Set the behavior and return result for method - saveAndCommit
        $this->bookChapterRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($this->callback(function (BookChapter $chapter) use ($book) {
                $expectedChapter = (new BookChapter())
                    ->setBook($book)
                    ->setSort(6)
                    ->setLevel(1)
                    ->setTitle('test')
                    ->setSlug('test')
                    ->setParent(null);

                $this->assertEquals($expectedChapter, $chapter);
                $this->setEntityId($chapter, 1);

                return true;
            }));

        // Request
        $payload = (new CreateBookChapterRequest())->setTitle('test');

        // Comparing the expected value with the actual returned value
        $this->assertEquals(
            new IdResponse(1),
            $this->createManager()->createChapter($payload, 1),
        );
    }

    public function testUpdateChapter(): void
    {
        // Create Book Chapter
        $chapter = new BookChapter();
        $newTitle = 'Updated Chapter';
        $newSlug = 'updated-chapter';

        // Set the behavior and return result for method - slug
        $this->slugger->expects($this->once())
            ->method('slug')
            ->with($newTitle)
            ->willReturn(new UnicodeString($newSlug));

        // Set the behavior and return result for method - getById
        $this->bookChapterRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($chapter);

        // Set the behavior for method - commit
        $this->bookChapterRepository->expects($this->once())
            ->method('commit');

        // Request
        $payload = (new UpdateBookChapterRequest())->setId(1)->setTitle($newTitle);

        // Run manager
        $this->createManager()->updateChapter($payload);

        // Comparing the expected value with the actual returned value
        $this->assertEquals($newTitle, $chapter->getTitle());
        $this->assertEquals($newSlug, $chapter->getSlug());
    }

    public function testDeleteChapter(): void
    {
        // Create chapter
        $chapter = new BookChapter();

        // Set the behavior and return result for method - getById
        $this->bookChapterRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($chapter);

        // Set the behavior and return result for method - removeAndCommit
        $this->bookChapterRepository->expects($this->once())
            ->method('removeAndCommit')
            ->with($chapter);

        // Run manager
        $this->createManager()->deleteChapter(1);
    }

    public function testGetChaptersTree(): void
    {
        // Create response
        $treeResponse = new BookChapterTreeResponse();
        // Create book
        $book = new Book();

        // Set the behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        // Set the behavior and return result for method - getChaptersTree
        $this->bookChapterManager->expects($this->once())
            ->method('getChaptersTree')
            ->with($book)
            ->willReturn($treeResponse);

        // Comparing the expected value with the actual returned value
        $this->assertEquals($treeResponse, $this->createManager()->getChaptersTree(1));
    }

    public function testUpdateChapterSortAsLast(): void
    {
        // Create book
        $book = new Book();
        // Create parent chapter
        $parentChapter = new BookChapter();
        // Create chapter
        $chapter = (new BookChapter())->setBook($book)->setParent(null);
        // Create near chapter
        $nearChapter = (new BookChapter())->setLevel(2)->setBook($book)->setParent($parentChapter);

        // Set the behavior and return result for method - getById
        $this->bookChapterRepository->expects($this->exactly(2))
            ->method('getById')
            ->withConsecutive([1], [5])
            ->willReturnOnConsecutiveCalls($chapter, $nearChapter);

        // Set the behavior and return result for method - getMaxSort
        $this->bookChapterRepository->expects($this->once())
            ->method('getMaxSort')
            ->with($book, 2)
            ->willReturn(5);

        // Set the behavior and return result for method - 'commit'
        $this->bookChapterRepository->expects($this->once())
            ->method('commit');

        // Create request
        $payload = (new UpdateBookChapterSortRequest())->setId(1)->setNextId(null)->setPreviousId(5);
        // Run AuthorBookChapterManager
        $this->createManager()->updateChapterSort($payload);

        // Comparing the expected value with the actual returned value
        $this->assertEquals(2, $chapter->getLevel());
        $this->assertEquals(6, $chapter->getSort());
        $this->assertEquals($parentChapter, $chapter->getParent());
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
