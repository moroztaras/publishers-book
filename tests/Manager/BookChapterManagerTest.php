<?php

declare(strict_types=1);

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Model\BookChapter as BookChapterModel;
use App\Model\BookChapterTreeResponse;
use App\Repository\BookChapterRepository;
use App\Manager\BookChapterManager;
use App\Tests\AbstractTestCase;
use App\Tests\MockUtils;

class BookChapterManagerTest extends AbstractTestCase
{
    private BookChapterRepository $bookChapterRepository;

    protected function setUp(): void
    {
        $this->bookChapterRepository = $this->createMock(BookChapterRepository::class);

        parent::setUp();
    }

    public function testGetChaptersTree(): void
    {
        // Create book
        $book = new Book();
        // Create response
        $expectedResponse = new BookChapterTreeResponse([
            new BookChapterModel(1, 'Test chapter', 'test-chapter', [
                new BookChapterModel(2, 'Test chapter', 'test-chapter'),
            ]),
        ]);

        // Create parent chapter
        $parentChapter = MockUtils::createBookChapter($book);
        $this->setEntityId($parentChapter, 1);

        // Create child chapter
        $childChapter = MockUtils::createBookChapter($book)->setParent($parentChapter);
        $this->setEntityId($childChapter, 2);

        // Set behavior and response for method - findSortedChaptersByBook
        $this->bookChapterRepository->expects($this->once())
            ->method('findSortedChaptersByBook')
            ->with($book)
            ->willReturn([$parentChapter, $childChapter]);

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(
            $expectedResponse,
            $this->createManager()->getChaptersTree($book),
        );
    }

    private function createManager(): BookChapterManager
    {
        return new BookChapterManager($this->bookChapterRepository);
    }
}
