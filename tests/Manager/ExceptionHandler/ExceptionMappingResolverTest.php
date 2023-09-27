<?php

namespace App\Tests\Manager\ExceptionHandler;

use App\Manager\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;
use InvalidArgumentException;

class ExceptionMappingResolverTest extends AbstractTestCase
{
    // Test for exception on empty code
    public function testThrowsExceptionOnEmptyCode(): void
    {
        // expect InvalidArgumentException
        $this->expectException(InvalidArgumentException::class);

        new ExceptionMappingResolver(['someClass' => ['hidden' => true]]);
    }
}
