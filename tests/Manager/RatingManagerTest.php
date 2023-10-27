<?php

namespace App\Tests\Manager;

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

        $this->assertEquals(
            $expectedRating,
            (new RatingManager($this->reviewRepository))->calcReviewRatingForBook(1, $total)
        );
    }

    public function testCalcReviewRatingForBookZeroTotal(): void
    {
        $this->reviewRepository->expects($this->never())->method('getBookTotalRatingSum');

        $this->assertEquals(
            0,
            (new RatingManager($this->reviewRepository))->calcReviewRatingForBook(1, 0)
        );
    }
}
