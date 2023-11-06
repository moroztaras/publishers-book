<?php

namespace App\Tests\Manager;

use App\Manager\Rating;
use App\Manager\RatingManager;
use App\Repository\ReviewRepository;
use App\Tests\AbstractTestCase;

class RatingManagerTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
    }

    public function provider(): array
    {
        return [
            [25, 20, 1.25],
            [0, 5, 0],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testCalcReviewRatingForBook(int $repositoryRatingSum, int $total, float $expectedRating): void
    {
        // Set behavior for getBookTotalRatingSum method
        $this->reviewRepository->expects($this->once())
            ->method('getBookTotalRatingSum')
            ->with(1)
            ->willReturn($repositoryRatingSum);

        // Set behavior for countByBookId method
        $this->reviewRepository->expects($this->once())
            ->method('countByBookId')
            ->with(1)
            ->willReturn($total);

        $this->assertEquals(
            new Rating($total, $expectedRating),
            (new RatingManager($this->reviewRepository))->calcReviewRatingForBook(1)
        );
    }

    public function testCalcReviewRatingForBookZeroTotal(): void
    {
        // Set behavior for getBookTotalRatingSum method
        $this->reviewRepository->expects($this->never())->method('getBookTotalRatingSum');

        // Set behavior for countByBookId method
        $this->reviewRepository->expects($this->once())
            ->method('countByBookId')
            ->with(1)
            ->willReturn(0);

        $this->assertEquals(
            new Rating(0, 0),
            (new RatingManager($this->reviewRepository))->calcReviewRatingForBook(1)   );
    }
}
