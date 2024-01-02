<?php

namespace App\Model;

class BookCategory
{
    public function __construct(
        private readonly int $id,
        private readonly string $title,
        private readonly string $slug
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
