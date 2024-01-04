<?php

namespace App\Manager;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Exception\BookChapterInvalidSortException;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\Author\UpdateBookChapterRequest;
use App\Model\Author\UpdateBookChapterSortRequest;
use App\Model\BookChapterTreeResponse;
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
        private readonly BookRepository $bookRepository,
        private readonly BookChapterRepository $bookChapterRepository,
        private readonly BookChapterManager $bookChapterManager,
        private readonly SluggerInterface $slugger
    ) {
    }

    public function getChaptersTree(int $bookId): BookChapterTreeResponse
    {
        return $this->bookChapterManager->getChaptersTree($this->bookRepository->getBookById($bookId));
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

        // Create new chapter of book
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

    public function updateChapter(UpdateBookChapterRequest $request, int $id): void
    {
        $chapter = $this->bookChapterRepository->getById($id);
        $title = $request->getTitle();
        $chapter->setTitle($title)->setSlug($this->slugger->slug($title)->toString());

        $this->bookChapterRepository->commit();
    }

    // For sorting chapters
    public function updateChapterSort(UpdateBookChapterSortRequest $request, int $id): void
    {
        $chapter = $this->bookChapterRepository->getById($id);
        $sortContext = SortContext::fromNeighbours($request->getNextId(), $request->getPreviousId());
        $nearChapter = $this->bookChapterRepository->getById($sortContext->getNearId());
        $level = $nearChapter->getLevel();

        if (SortPosition::AsLast === $sortContext->getPosition()) {
            $sort = $this->getNextMaxSort($chapter->getBook(), $level);
        } else {
            $sort = $nearChapter->getSort();
            $this->bookChapterRepository->increaseSortFrom($sort, $chapter->getBook(), $level, self::SORT_STEP);
        }

        $chapter->setLevel($level)->setSort($sort)->setParent($nearChapter->getParent());

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
