<?php

namespace App\Tests\Repository;

use App\Entity\BookContent;
use App\Repository\BookContentRepository;
use App\Tests\AbstractTestRepository;
use App\Tests\MockUtils;

class BookContentRepositoryTest extends AbstractTestRepository
{
    private BookContentRepository $bookContentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookContentRepository = $this->getRepositoryForEntity(BookContent::class);
    }

    public function testGetById()
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        $this->em->persist($book);
        // Create chapter
        $chapter = MockUtils::createBookChapter($book);
        $this->em->persist($chapter);
        // Create content
        $content = MockUtils::createBookContent($chapter);
        $this->em->persist($content);
        // Save
        $this->em->flush();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals($content->getContent(), $this->bookContentRepository->getById($content->getId())->getContent());
    }
}
