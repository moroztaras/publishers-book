<?php

namespace App\Exception;

class BookCategoryNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book category not found');
    }
}
