<?php

namespace App\Exception;

class BookFormatNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book format not found');
    }
}
