<?php

namespace App\Model;

class BookCategoryListItem
{
    public function __construct(
        private int $id,
        private string $title,
        private string $slug
    ) {
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
}
