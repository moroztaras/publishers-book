<?php

namespace App\Manager;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Exception\BookChapterInvalidSortException;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\Author\UpdateBookChapterRequest;
use App\Model\Author\UpdateBookChapterSortRequest;
use App\Model\BookChapterTreeResponse;
use App\Model\BookChapter as BookChapterModel;
use App\Model\IdResponse;
use App\Repository\BookChapterRepository;
use App\Repository\BookRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookChapterManager
{
    public function __construct(private BookChapterRepository $bookChapterRepository)
    {
    }

    public function getChaptersTree(Book $book): BookChapterTreeResponse
    {
        $chapters = $this->bookChapterRepository->findSortedChaptersByBook($book);
        $response = new BookChapterTreeResponse();
        /** @var array<int, BookChapterModel> $index */
        $index = [];

        foreach ($chapters as $chapter) {
            $model = new BookChapterModel($chapter->getId(), $chapter->getTitle(), $chapter->getSlug());
            $index[$chapter->getId()] = $model;

            if (!$chapter->hasParent()) {
                $response->addItem($model);
                continue;
            }

            $parent = $chapter->getParent();
            $index[$parent->getId()]->addItem($model);
        }

        return $response;
    }
}
