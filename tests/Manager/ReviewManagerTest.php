<?php

namespace App\Tests\Manager;

use App\Entity\Review;
use App\Manager\Rating;
use App\Manager\RatingManager;
use App\Manager\ReviewManager;
use App\Model\Review as ReviewModel;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;
use App\Tests\AbstractTestCase;

class ReviewManagerTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;

    private RatingManager $ratingManager;

    private const BOOK_ID = 1;

    private const PER_PAGE = 5;

    protected function setUp(): void
    {
        parent::setUp();
        // Set mock repositories
        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->ratingManager = $this->createMock(RatingManager::class);
    }

    public function dataProvider(): array
    {
        return [
            [0, 0],
            [-1, 0],
            [-20, 0],
        ];
    }

    // Not valid page review
    /**
     * @dataProvider dataProvider
     */
    public function testGetReviewPageByBookIdInvalidPage(int $page, int $offset): void
    {
        // Set the behavior & expected for the method - calcReviewRatingForBook
        $this->ratingManager->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID)
            ->willReturn(new Rating(0, 0.0));

        // Set the behavior & expected for the method - getPageByBookId
        $this->reviewRepository->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, $offset, self::PER_PAGE)
            ->willReturn(new \ArrayIterator());

        // Create manager
        $manager = new ReviewManager($this->reviewRepository, $this->ratingManager);
        // Expected value
        $expected = (new ReviewPage())->setTotal(0)->setRating(0)->setPage($page)->setPages(0)
            ->setPerPage(self::PER_PAGE)->setItems([]);

        // Comparing the expected value with the actual returned value .
        $this->assertEquals($expected, $manager->getReviewPageByBookId(self::BOOK_ID, $page));
    }

    public function testGetReviewPageByBookId(): void
    {
        // Set the behavior & expected for the method - calcReviewRatingForBook
        $this->ratingManager->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID)
            ->willReturn(new Rating(1, 4.0));

        // Create entity & set id
        $entity = (new Review())->setAuthor('tester')->setContent('test content')
            ->setCreatedAt(new \DateTimeImmutable('2020-10-10'))->setRating(4);

        $this->setEntityId($entity, 1);

        // Set the behavior & expected for the method - getPageByBookId
        $this->reviewRepository->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, 0, self::PER_PAGE)
            ->willReturn(new \ArrayIterator([$entity]));

        // Create ReviewManager
        $manager = new ReviewManager($this->reviewRepository, $this->ratingManager);
        // Expected value
        $expected = (new ReviewPage())->setTotal(1)->setRating(4)->setPage(1)->setPages(1)
            ->setPerPage(self::PER_PAGE)->setItems([
                (new ReviewModel())->setId(1)->setRating(4)->setCreatedAt(1602288000)
                    ->setContent('test content')->setAuthor('tester'),
            ]);

        // Comparing the expected value with the actual returned value .
        $this->assertEquals($expected, $manager->getReviewPageByBookId(self::BOOK_ID, 1));
    }
}
