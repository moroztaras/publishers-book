<?php

namespace App\Manager\Recommendation\Model;

class RecommendationResponse
{
    public function __construct(private readonly int $id, private readonly int $ts, private readonly array $recommendations)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTs(): int
    {
        return $this->ts;
    }

    /**
     * @return RecommendationItem[]
     */
    public function getRecommendations(): array
    {
        return $this->recommendations;
    }
}
