<?php

namespace App\Exception;

use RuntimeException;

class BookCategoryNotEmptyException extends RuntimeException
{
    public function __construct(int $booksCount)
    {
        parent::__construct(sprintf('Book category has %d books', $booksCount));
    }
}
