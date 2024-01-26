<?php

namespace App\Tests\Repository;

use App\Entity\BookChapter;
use App\Exception\BookChapterNotFoundException;
use App\Repository\BookChapterRepository;
use App\Tests\AbstractTestController;
use App\Tests\AbstractTestRepository;
use App\Tests\MockUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookChapterRepositoryTest extends AbstractTestRepository
{
    private BookChapterRepository $bookChapterRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookChapterRepository = $this->getRepositoryForEntity(BookChapter::class);
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

        // Save
        $this->em->flush();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals($chapter->getTitle(), $this->bookChapterRepository->getById($chapter->getId())->getTitle());
    }

    public function testGetByIdNotFound()
    {
        $this->expectException(BookChapterNotFoundException::class);

        $this->bookChapterRepository->getById(1);
    }

    public function testFindSortedChaptersByBook()
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        $this->em->persist($book);
        // Create chapters
        $firstChapter = MockUtils::createBookChapter($book);
        $this->em->persist($firstChapter);
        $secondChapter = MockUtils::createBookChapter($book)->setLevel(2);
        $this->em->persist($secondChapter);
        $secondChapter = MockUtils::createBookChapter($book)->setLevel(3);
        $this->em->persist($secondChapter);

        // Save
        $this->em->flush();

        $expectedChapters = $this->bookChapterRepository->findSortedChaptersByBook($book);

        // Comparing the expected value with the actual returned value.
        $this->assertCount(3, $expectedChapters);
        $this->assertEquals($firstChapter, $expectedChapters[0]);
        $this->assertEquals($secondChapter, $expectedChapters[2]);
    }
}
