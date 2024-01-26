<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Exception\BookNotFoundException;
use App\Repository\BookRepository;
use App\Tests\AbstractTestRepository;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;

class BookRepositoryTest extends AbstractTestRepository
{
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->getRepositoryForEntity(Book::class);
    }

    public function testFindBooksByCategoryId()
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);
        // Create category
        $devicesCategory = MockUtils::createBookCategory();
        $this->em->persist($devicesCategory);

        for ($i = 0; $i < 5; ++$i) {
            // Create book
            $book = MockUtils::createBook()->setUser($user)
                ->setTitle('device-'.$i)
                ->setCategories(new ArrayCollection([$devicesCategory]));

            $this->em->persist($book);
        }

        $this->em->flush();
        // Comparing the expected value with the actual returned value.
        $this->assertCount(5, $this->bookRepository->findPublishedBooksByCategoryId($devicesCategory->getId()));
    }

    public function testGetPublishedById()
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);
        // Create category
        $devicesCategory = MockUtils::createBookCategory();
        $this->em->persist($devicesCategory);

        // Create book
        $book = MockUtils::createBook()->setUser($user)
            ->setCategories(new ArrayCollection([$devicesCategory]));
        $this->em->persist($book);

        $this->em->flush();

        // Comparing the expected value with the actual returned value.
        $this->assertEquals($book->getTitle(), $this->bookRepository->getPublishedById($book->getId())->getTitle());
    }

    public function testGetPublishedByIdBookNotFound()
    {
        // Expect exception
        $this->expectException(BookNotFoundException::class);
        // Ren method
        $this->bookRepository->getPublishedById(1);
    }

    public function testFindBooksByIds()
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);
        // Create category
        $devicesCategory = MockUtils::createBookCategory();
        $this->em->persist($devicesCategory);

        for ($i = 0; $i < 5; ++$i) {
            // Create book
            $book = MockUtils::createBook()->setUser($user)
                ->setCategories(new ArrayCollection([$devicesCategory]));

            $this->em->persist($book);

            $ids[] = $book->getId();
        }

        $this->em->flush();

        // Comparing the expected value with the actual returned value.
        $this->assertCount(5, $this->bookRepository->findBooksByIds($ids ?? []));
    }
}
