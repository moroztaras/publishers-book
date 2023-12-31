<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTest;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;

class BookRepositoryTest extends AbstractRepositoryTest
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
}
