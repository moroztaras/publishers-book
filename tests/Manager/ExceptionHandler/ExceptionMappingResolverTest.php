<?php

namespace App\Tests\Manager\ExceptionHandler;

use App\Manager\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class ExceptionMappingResolverTest extends AbstractTestCase
{
    // Test for exception on empty code
    public function testThrowsExceptionOnEmptyCode(): void
    {
        // expect InvalidArgumentException
        $this->expectException(\InvalidArgumentException::class);

        new ExceptionMappingResolver(['someClass' => ['hidden' => true]]);
    }

    // If not found class in settings
    public function testResolvesToNullWhenNotFound(): void
    {
        $resolver = new ExceptionMappingResolver([]);

        $this->assertNull($resolver->resolve(\InvalidArgumentException::class));
    }

    // When can find a class that matches what pass.
    public function testResolvesClassItself(): void
    {
        // Create resolver
        $resolver = new ExceptionMappingResolver([\InvalidArgumentException::class => ['code' => Response::HTTP_BAD_REQUEST]]);
        // To resolve
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        // Comparing the actual returned value with the expected value.
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $mapping->getCode());
        $this->assertTrue($mapping->isHidden());
        $this->assertFalse($mapping->isLoggable());
    }

    // When don't have a specific class, but there is its parent.
    public function testResolvesSubClass(): void
    {
        // Create resolver
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => Response::HTTP_INTERNAL_SERVER_ERROR]]);
        // To resolve
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        // Comparing the actual returned value with the expected value.
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $mapping->getCode());
    }
}
