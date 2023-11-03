<?php

namespace App\Manager\Recommendation\Exception;

final class AccessDeniedException extends RecommendationException
{
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct('Access denied', $previous);
    }
}
