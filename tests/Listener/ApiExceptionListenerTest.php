<?php

namespace App\Tests\Listener;

use App\Listener\ApiExceptionListener;
use App\Manager\ExceptionHandler\ExceptionMapping;
use App\Manager\ExceptionHandler\ExceptionMappingResolver;
use App\Model\ErrorResponse;
use App\Tests\AbstractTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListenerTest extends AbstractTestCase
{
    private ExceptionMappingResolver $resolver;

    private LoggerInterface $logger;

    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock of dependencies
        $this->resolver = $this->createMock(ExceptionMappingResolver::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    // The mapping is non 500 with hidden message
    public function testNon500MappingWithHiddenMessage(): void
    {
        // Create mapping
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        // Create response message
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        // Create serialize response body
        $responseBody = json_encode(['error' => $responseMessage]);

        // Set the behavior of the method - resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        // Set the behavior of the method - serialize.
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Create an event to send.
        $event = $this->createEvent(new \InvalidArgumentException('test'));

        // Run event listener.
        $this->runListener($event);

        // Comparing the expected value with the actual returned response.
        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    // Mapping with hidden - false
    public function testNon500MappingWithPublicMessage(): void
    {
        // Create mapping
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, false);
        // Create response message
        $responseMessage = 'test';
        // Create response body
        $responseBody = json_encode(['error' => $responseMessage]);

        // Set the behavior of the method - resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        // Set the behavior of the method - serialize
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Create an event to send.
        $event = $this->createEvent(new \InvalidArgumentException('test'));

        // Run event listener.
        $this->runListener($event);

        // Comparing the expected value with the actual returned response.
        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    // Test with configured logger. The logger is really challenged
    public function testNon500LoggableMappingTriggersLogger(): void
    {
        // Create mapping
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, true);
        // Create response message
        $responseMessage = 'test';
        // Create response body
        $responseBody = json_encode(['error' => $responseMessage]);

        // Set the behavior of the method - resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        // Set the behavior of the method - serialize
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Expect logger
        $this->logger->expects($this->once())
            ->method('error');

        // Create an event to send.
        $event = $this->createEvent(new \InvalidArgumentException('test'));

        // Run event listener.
        $this->runListener($event);

        // Comparing the expected value with the actual returned response.
        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    // Test with configured logger end error 500.
    public function test500IsLoggable(): void
    {
        // Create mapping
        $mapping = ExceptionMapping::fromCode(Response::HTTP_GATEWAY_TIMEOUT);
        // Create response message
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        // Create response body
        $responseBody = json_encode(['error' => $responseMessage]);

        // Set the behavior of the method - resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        // Set the behavior of the method - serialize
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Logger settings
        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything());

        $event = $this->createEvent(new \InvalidArgumentException('error message'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_GATEWAY_TIMEOUT, $responseBody, $event->getResponse());
    }

    private function createEvent(\InvalidArgumentException $e): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createTestKernel(),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST, // The main request that comes from the client.
            $e
        );
    }

    private function runListener(ExceptionEvent $event, bool $isDebug = false): void
    {
        // Create listener
        (new ApiExceptionListener($this->resolver, $this->logger, $this->serializer, $isDebug))($event);
    }

    private function assertResponse(int $expectedStatusCode, string $expectedBody, Response $actualResponse): void
    {
        // Check status code
        $this->assertEquals($expectedStatusCode, $actualResponse->getStatusCode());
        // Check response
        $this->assertInstanceOf(JsonResponse::class, $actualResponse);
        // Check body
        $this->assertJsonStringEqualsJsonString($expectedBody, $actualResponse->getContent());
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
