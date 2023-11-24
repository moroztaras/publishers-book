<?php

namespace App\Exception;

use RuntimeException;

class BookAlreadyExistsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book already exists');
    }
}
