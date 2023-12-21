<?php

namespace App\Exception;

use RuntimeException;

class BookChapterNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book chapter not found');
    }
}
