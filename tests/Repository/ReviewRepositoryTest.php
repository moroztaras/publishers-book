<?php

namespace App\Tests\Repository;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use App\Tests\AbstractTestRepository;
use App\Tests\MockUtils;

class ReviewRepositoryTest extends AbstractTestRepository
{
    private ReviewRepository $reviewRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->getRepositoryForEntity(Review::class);
    }

    public function testCountByBookId()
    {
        // Create user
        $user = MockUtils::createUser();
        $this->em->persist($user);
        // Create book
        $book = MockUtils::createBook()->setUser($user);
        $this->em->persist($book);
        // Create Review
        $this->em->persist(MockUtils::createReview($book));

        // Save
        $this->em->flush();

        // Comparing the expected value with the actual returned value.
        $this->assertIsNumeric($this->reviewRepository->countByBookId($book->getId()));
        $this->assertEquals(1, $this->reviewRepository->countByBookId($book->getId()));
    }
}
