<?php

namespace App\Manager;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Exception\BookChapterInvalidSortException;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\Author\UpdateBookChapterRequest;
use App\Model\IdResponse;
use App\Repository\BookChapterRepository;
use App\Repository\BookRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookChapterManager
{
    private const MAX_LEVEL = 3;

    private const MIN_LEVEL = 1;

    private const SORT_STEP = 1;

    public function __construct(
        private BookRepository        $bookRepository,
        private BookChapterRepository $bookChapterRepository,
        private SluggerInterface      $slugger
    ) {
    }

    public function createChapter(CreateBookChapterRequest $request, int $bookId): IdResponse
    {
        $book = $this->bookRepository->getBookById($bookId);
        $title = $request->getTitle();
        $parentId = $request->getParentId();
        $parent = null;
        $level = self::MIN_LEVEL;

        if (null !== $parentId) {
            $parent = $this->bookChapterRepository->getById($parentId);
            $parentLevel = $parent->getLevel();
            if (self::MAX_LEVEL === $parentLevel) {
                throw new BookChapterInvalidSortException('Max level is reached');
            }

            $level = $parentLevel + 1;
        }

        $chapter = (new BookChapter())
            ->setTitle($title)
            ->setSlug($this->slugger->slug($title))
            ->setParent($parent)
            ->setLevel($level)
            ->setSort($this->getNextMaxSort($book, $level))
            ->setBook($book);

        $this->bookChapterRepository->saveAndCommit($chapter);

        return new IdResponse($chapter->getId());
    }

    public function updateChapter(UpdateBookChapterRequest $request): void
    {
        $chapter = $this->bookChapterRepository->getById($request->getId());
        $title = $request->getTitle();
        $chapter->setTitle($title)->setSlug($this->slugger->slug($title));

        $this->bookChapterRepository->commit();
    }

    public function deleteChapter(int $id): void
    {
        $chapter = $this->bookChapterRepository->getById($id);

        $this->bookChapterRepository->removeAndCommit($chapter);
    }

    private function getNextMaxSort(Book $book, int $level): int
    {
        return $this->bookChapterRepository->getMaxSort($book, $level) + self::SORT_STEP;
    }
}
