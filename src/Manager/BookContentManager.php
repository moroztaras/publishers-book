<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\BookContent;
use App\Model\Author\CreateBookChapterContentRequest;
use App\Model\IdResponse;
use App\Repository\BookChapterRepository;
use App\Repository\BookContentRepository;

class BookContentManager
{
    public function __construct(
        private readonly BookContentRepository $bookContentRepository,
        private readonly BookChapterRepository $bookChapterRepository)
    {
    }

    public function createContent(CreateBookChapterContentRequest $request, int $chapterId): IdResponse
    {
        $content = (new BookContent())->setChapter($this->bookChapterRepository->getById($chapterId));

        $this->saveContent($request, $content);

        return new IdResponse($content->getId());
    }

    public function updateContent(CreateBookChapterContentRequest $request, int $id): void
    {
        $content = $this->bookContentRepository->getById($id);

        $this->saveContent($request, $content);
    }

    public function deleteContent(int $id): void
    {
        $content = $this->bookContentRepository->getById($id);

        $this->bookContentRepository->removeAndCommit($content);
    }

    private function saveContent(CreateBookChapterContentRequest $request, BookContent $content): void
    {
        $content->setContent($request->getContent());
        $content->setIsPublished($request->isPublished());

        $this->bookContentRepository->saveAndCommit($content);
    }
}
