<?php

namespace App\Tests\Listener;

use App\Listener\ApiExceptionListener;
use App\Manager\ExceptionHandler\ExceptionMapping;
use App\Manager\ExceptionHandler\ExceptionMappingResolver;
use App\Model\ErrorDebugDetails;
use App\Model\ErrorResponse;
use App\Tests\AbstractTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
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
        $event = $this->createExceptionEvent(new \InvalidArgumentException('test'));

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
        $event = $this->createExceptionEvent(new \InvalidArgumentException('test'));

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
        $event = $this->createExceptionEvent(new \InvalidArgumentException('test'));

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

        // Set the behavior logger and the method - error with arguments
        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything());

        // Create event
        $event = $this->createExceptionEvent(new \InvalidArgumentException('error message'));

        // Run listener
        $this->runListener($event);

        // Comparing the expected value with the actual returned response.
        $this->assertResponse(Response::HTTP_GATEWAY_TIMEOUT, $responseBody, $event->getResponse());
    }

    // When resolve return null
    public function test500IsDefaultWhenMappingNotFound(): void
    {
        // Create response message
        $responseMessage = Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        // Create response body
        $responseBody = json_encode(['error' => $responseMessage]);

        // Set the behavior of the method - resolve
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn(null);

        // Set the behavior of the method - serialize
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        // Set the behavior logger and the method - error with arguments
        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything());

        // Create event
        $event = $this->createExceptionEvent(new \InvalidArgumentException('error message'));

        // Run Listener
        $this->runListener($event);

        // Comparing the expected value with the actual returned response.
        $this->assertResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $responseBody, $event->getResponse());
    }

    // When in debug mode and show the trace
    public function testShowTraceWhenDebug(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage, 'trace' => 'something']);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->callback(function (ErrorResponse $response) use ($responseMessage) {
                    /** @var ErrorDebugDetails|object $details */
                    $details = $response->getDetails();

                    return $response->getMessage() == $responseMessage
                        && $details instanceof ErrorDebugDetails && !empty($details->getTrace());
                }),
                JsonEncoder::FORMAT
            )
            ->willReturn($responseBody);

        // Create event
        $event = $this->createExceptionEvent(new \InvalidArgumentException('error message'));

        // Run listener
        $this->runListener($event, true);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function testIgnoreSecurityException(): void
    {
        // Set the behavior logger and the method - resolve
        $this->resolver->expects($this->never())
            ->method('resolve');

        // Create event
        $event = $this->createExceptionEvent(new AuthenticationException());
        // Run listener
        $this->runListener($event, true);
    }

    // Run listener
    private function runListener(ExceptionEvent $event, bool $isDebug = false): void
    {
        // Create listener
        (new ApiExceptionListener($this->resolver, $this->logger, $this->serializer, $isDebug))($event);
    }
}
