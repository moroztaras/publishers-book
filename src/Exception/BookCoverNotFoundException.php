<?php

namespace App\Exception;

class BookCoverNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book cover not found.');
    }
}
