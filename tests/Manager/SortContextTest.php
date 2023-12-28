<?php

namespace App\Tests\Manager;

use App\Manager\SortContext;
use App\Manager\SortPosition;
use App\Tests\AbstractTestCase;

class SortContextTest extends AbstractTestCase
{
    /**
     * @dataProvider neighboursProvider
     */
    public function testFromNeighbours(?int $nextId, ?int $previousId, SortPosition $expectedPosition, int $expectedNearId): void
    {
        $sortContext = SortContext::fromNeighbours($nextId, $previousId);

        $this->assertEquals($expectedPosition, $sortContext->getPosition());
        $this->assertEquals($expectedNearId, $sortContext->getNearId());
    }

    public function neighboursProvider(): array
    {
        return [
            [null, 2, SortPosition::AsLast, 2],
            [3, null, SortPosition::AsFirst, 3],
            [6, 5, SortPosition::Between, 6],
        ];
    }
}
