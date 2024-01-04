<?php

namespace App\Model\Author;

class BookListResponse
{
    /**
     * @param BookListItem[] $items
     */
    public function __construct(private readonly array $items)
    {
    }

    /**
     * @return BookListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
