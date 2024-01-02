<?php

namespace App\Model;

class BookChapter
{
    /**
     * @param BookChapter[] $items
     */
    public function __construct(
        private readonly int $id,
        private readonly string $title,
        private readonly string $slug,
        private readonly array $items = [])
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return BookChapter[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(BookChapter $chapter): void
    {
        $this->items[] = $chapter;
    }
}
