<?php

namespace App\Exception;

class BookChapterNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book chapter not found');
    }
}
