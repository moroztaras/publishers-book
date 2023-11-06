<?php

namespace App\Tests\Manager;

use App\Entity\Book;
use App\Manager\Recommendation\Model\RecommendationItem;
use App\Manager\Recommendation\Model\RecommendationResponse;
use App\Manager\Recommendation\RecommendationApiManager;
use App\Manager\RecommendationManager;
use App\Model\RecommendedBook;
use App\Model\RecommendedBookListResponse;
use App\Repository\BookRepository;
use App\Tests\AbstractTestCase;

class RecommendationManagerTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    private RecommendationApiManager $recommendationApiManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->recommendationApiManager = $this->createMock(RecommendationApiManager::class);
    }

    public function dataProvider(): array
    {
        return [
            ['short description', 'short description'],
            [
                <<<EOF
begin long description long description
long description long description long
long description long description
long description long description
description
EOF,
                <<<EOF
begin long description long description
long description long description long
long description long description
long description long description
...
EOF,
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetRecommendationsByBookId(string $actualDescription, string $expectedDescription): void
    {
        // Create book
        $book = (new Book())
            ->setImage('image')
            ->setSlug('slug')
            ->setTitle('title')
            ->setDescription($actualDescription)
        ;

        // Set id to book
        $this->setEntityId($book, 2);

        // Set the behavior of findBooksByIds method
        $this->bookRepository->expects($this->once())
            ->method('findBooksByIds')
            ->with([2])
            ->willReturn([$book])
        ;

        // Set the behavior of getRecommendationsByBookId method
        $this->recommendationApiManager->expects($this->once())
            ->method('getRecommendationsByBookId')
            ->with(1)
            ->willReturn(new RecommendationResponse(1, 12345, [new RecommendationItem(2)]))
        ;

        // Expected value
        $expected = new RecommendedBookListResponse([
            (new RecommendedBook())
                ->setId(2)
                ->setTitle('title')
                ->setSlug('slug')
                ->setImage('image')
                ->setShortDescription($expectedDescription),
        ]);

        // Comparing the expected value with the actual returned value
        $this->assertEquals($expected, $this->createManager()->getRecommendationsByBookId(1));
    }

    private function createManager(): RecommendationManager
    {
        return new RecommendationManager($this->bookRepository, $this->recommendationApiManager);
    }
}
