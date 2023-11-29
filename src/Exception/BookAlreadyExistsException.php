<?php

namespace App\Exception;

class BookAlreadyExistsException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book already exists');
    }
}
