<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\BookContent;
use App\Model\Author\CreateBookChapterContentRequest;
use App\Model\BookChapterContent;
use App\Model\BookChapterContentPage;
use App\Model\IdResponse;
use App\Repository\BookChapterRepository;
use App\Repository\BookContentRepository;

class BookContentManager
{
    private const PAGE_LIMIT = 30;

    public function __construct(
        private readonly BookContentRepository $bookContentRepository,
        private readonly BookChapterRepository $bookChapterRepository)
    {
    }

    public function getAllContent(int $chapterId, int $page): BookChapterContentPage
    {
        return $this->getContent($chapterId, $page, false);
    }

    public function getPublishedContent(int $chapterId, int $page): BookChapterContentPage
    {
        return $this->getContent($chapterId, $page, true);
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
        $content->setContent($request->getContent())->setIsPublished($request->isPublished());

        $this->bookContentRepository->saveAndCommit($content);
    }

    private function getContent(int $chapterId, int $page, bool $onlyPublished): BookChapterContentPage
    {
        $items = [];
        // Get paginator
        $paginator = $this->bookContentRepository->getPageByChapterId(
            $chapterId,
            $onlyPublished,
            PaginationUtils::calcOffset($page, self::PAGE_LIMIT),
            self::PAGE_LIMIT
        );

        foreach ($paginator as $item) {
            // Remap item into BookChapterContent model
            $items[] = (new BookChapterContent())
                ->setId($item->getId())
                ->setContent($item->getContent())
                ->setIsPublished($item->isPublished());
        }

        $total = $this->bookContentRepository->countByChapterId($chapterId, $onlyPublished);

        return (new BookChapterContentPage())
            ->setTotal($total)
            ->setPage($page)
            ->setPerPage(self::PAGE_LIMIT)
            ->setPages(PaginationUtils::calcPages($total, self::PAGE_LIMIT))
            ->setItems($items);
    }
}
