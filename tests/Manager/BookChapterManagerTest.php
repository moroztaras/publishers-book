<?php

namespace App\Tests\Service;

use App\Repository\BookChapterRepository;
use App\Tests\AbstractTestCase;

class BookChapterServiceTest extends AbstractTestCase
{
    private BookChapterRepository $bookChapterRepository;

    protected function setUp(): void
    {
        $this->bookChapterRepository = $this->createMock(BookChapterRepository::class);

        parent::setUp();
    }

    //TODO Need write this test
    public function testGetChaptersTree(): void
    {
    }
}
