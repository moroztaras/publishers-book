<?php

declare(strict_types=1);

namespace App\Exception;

class BookChapterContentNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book chapter content not found');
    }
}
