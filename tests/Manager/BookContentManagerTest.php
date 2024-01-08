<?php

declare(strict_types=1);

namespace App\Tests\Manager;

use App\Exception\BookChapterNotFoundException;
use App\Manager\BookContentManager;
use App\Model\Author\CreateBookChapterContentRequest;
use App\Repository\BookChapterRepository;
use App\Repository\BookContentRepository;
use App\Tests\AbstractTestCase;

class BookContentManagerTest extends AbstractTestCase
{
    private const PER_PAGE = 30;

    private BookChapterRepository $bookChapterRepository;
    private BookContentRepository $bookContentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookChapterRepository = $this->createMock(BookChapterRepository::class);
        $this->bookContentRepository = $this->createMock(BookContentRepository::class);
    }

    public function testCreateContentException(): void
    {
        // Expect exception
        $this->expectException(BookChapterNotFoundException::class);

        // Set behavior and response for method - getById
        $this->bookChapterRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willThrowException(new BookChapterNotFoundException());

        $this->createManager()->createContent(new CreateBookChapterContentRequest(), 1);
    }

    private function createManager(): BookContentManager
    {
        return new BookContentManager($this->bookContentRepository, $this->bookChapterRepository);
    }
}
