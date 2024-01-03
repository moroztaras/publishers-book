<?php

namespace App\Model;

class BookCategoryListResponse
{
    /**
     * @param BookCategory[] $items
     */
    public function __construct(private readonly array $items)
    {
    }

    /**
     * @return BookCategory[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
