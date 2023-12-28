<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Manager\BookPublishManager;
use App\Model\Author\PublishBookRequest;
use App\Repository\BookRepository;
use App\Tests\AbstractTestCase;

class BookPublishManagerTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->createMock(BookRepository::class);
    }

    public function testPublish(): void
    {
        $book = new Book();
        $datetime = new \DateTimeImmutable('2020-10-10');
        $request = new PublishBookRequest();
        $request->setDate($datetime);

        // Set behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('commit');

        // Run method - publish
        (new BookPublishManager($this->bookRepository))->publish(1, $request);

        // Check data with book publication date
        $this->assertEquals($datetime, $book->getPublicationDate());
    }

    public function testUnPublish(): void
    {
        $book = (new Book())->setPublicationDate(new \DateTimeImmutable('2020-10-10'));

        // Set behavior and return result for method - getBookById
        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('commit');

        // Run method - unPublish
        (new BookPublishManager($this->bookRepository))->unPublish(1);

        $this->assertNull($book->getPublicationDate());
    }
}
