<?php

declare(strict_types=1);

namespace App\Tests\Manager;

use App\Entity\BookChapter;
use App\Entity\BookContent;
use App\Exception\BookChapterContentNotFoundException;
use App\Exception\BookChapterNotFoundException;
use App\Manager\BookContentManager;
use App\Model\Author\CreateBookChapterContentRequest;
use App\Model\IdResponse;
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

        // Run Book Content Manager
        $this->createManager()->createContent(new CreateBookChapterContentRequest(), 1);
    }

    public function testCreateContent(): void
    {
        // Create request
        $payload = (new CreateBookChapterContentRequest())
            ->setContent('testing')
            ->setIsPublished(true);

        // Create chapter
        $chapter = new BookChapter();
        $expectedContent = (new BookContent())
            ->setContent('testing')
            ->setIsPublished(true)
            ->setChapter($chapter);

        // Set behavior and response for method - getById
        $this->bookChapterRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($chapter);

        // Set behavior and response for method - saveAndCommit
        $this->bookContentRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($expectedContent)
            ->will($this->returnCallback(function (BookContent $content) {
                $this->setEntityId($content, 2);
            }));

        // Comparing the expected value with the actual returned value.
        $this->assertEquals(new IdResponse(2), $this->createManager()->createContent($payload, 1));
    }

    public function testUpdateContentException(): void
    {
        // Expect exception
        $this->expectException(BookChapterContentNotFoundException::class);

        // Set behavior and response for method - getById
        $this->bookContentRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willThrowException(new BookChapterContentNotFoundException());

        // Run Book Content Manager
        $this->createManager()->updateContent(new CreateBookChapterContentRequest(), 1);

    }

    public function testUpdateContent(): void
    {
        // Request
        $payload = (new CreateBookChapterContentRequest())
            ->setContent('initial')
            ->setIsPublished(false);

        // Create chapter
        $chapter = new BookChapter();
        // Create chapter content
        $content = (new BookContent())->setChapter($chapter);

        $expectedContent = (new BookContent())
            ->setContent('initial')
            ->setIsPublished(false)
            ->setChapter($chapter);

        // Set behavior and response for method - getById
        $this->bookContentRepository->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($content);

        // Set behavior for method - saveAndCommit
        $this->bookContentRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($expectedContent);

        // Run manager
        $this->createManager()->updateContent($payload, 2);
    }

    public function testDeleteContentException(): void
    {
        // Expect exception
        $this->expectException(BookChapterContentNotFoundException::class);

        // Set behavior and response for method - getById
        $this->bookContentRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willThrowException(new BookChapterContentNotFoundException());

        // Run manager
        $this->createManager()->deleteContent(1);
    }

    public function testDeleteContent(): void
    {
        // Create book context
        $content = new BookContent();

        // Set behavior and response for method - getById
        $this->bookContentRepository->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($content);

        // Set behavior for method - removeAndCommit
        $this->bookContentRepository->expects($this->once())
            ->method('removeAndCommit')
            ->with($content);

        // Run manager
        $this->createManager()->deleteContent(1);
    }

    private function createManager(): BookContentManager
    {
        return new BookContentManager($this->bookContentRepository, $this->bookChapterRepository);
    }
}
