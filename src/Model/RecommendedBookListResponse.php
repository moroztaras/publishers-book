<?php

namespace App\Model;

class RecommendedBookListResponse
{
    /**
     * @param RecommendedBook[] $items
     */
    public function __construct(private readonly array $items)
    {
    }

    /**
     * @return RecommendedBook[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
