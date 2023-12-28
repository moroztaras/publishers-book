<?php

namespace App\Manager;

use App\Model\Author\PublishBookRequest;
use App\Repository\BookRepository;

class BookPublishManager
{
    public function __construct(private BookRepository $bookRepository)
    {
    }

    // Set date publish
    public function publish(int $id, PublishBookRequest $publishBookRequest): void
    {
        $this->setPublicationDate($id, $publishBookRequest->getDate());
    }

    // Remove date publish
    public function unPublish(int $id): void
    {
        $this->setPublicationDate($id, null);
    }

    private function setPublicationDate(int $id, ?\DateTimeInterface $dateTime): void
    {
        $book = $this->bookRepository->getBookById($id);
        $book->setPublicationDate($dateTime);

        $this->bookRepository->commit();
    }
}
