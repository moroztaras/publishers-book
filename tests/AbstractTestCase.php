<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class AbstractTestCase extends TestCase
{
    protected function setEntityId(object $entity, int $value, $idField = 'id')
    {
        $class = new \ReflectionClass($entity);
        // Get field id
        $property = $class->getProperty($idField);
        // Open this field id
        $property->setAccessible(true);
        // Set value to field id
        $property->setValue($entity, $value);
        // Close this field id
        $property->setAccessible(false);
    }

    // Check response
    protected function assertResponse(int $expectedStatusCode, string $expectedBody, Response $actualResponse): void
    {
        // Check status code
        $this->assertEquals($expectedStatusCode, $actualResponse->getStatusCode());
        // Check response
        $this->assertInstanceOf(JsonResponse::class, $actualResponse);
        // Check body
        $this->assertJsonStringEqualsJsonString($expectedBody, $actualResponse->getContent());
    }

    // Exception event
    protected function createExceptionEvent(\InvalidArgumentException $e): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createTestKernel(),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST, // The main request that comes from the client.
            $e
        );
    }

    // Create test kernel
    private function createTestKernel(): HttpKernelInterface
    {
        return new class() implements HttpKernelInterface {
            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                return new Response('test');
            }
        };
    }
}
