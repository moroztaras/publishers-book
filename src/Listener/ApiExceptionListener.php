<?php

namespace App\Listener;

use App\Manager\ExceptionHandler\ExceptionMapping;
use App\Manager\ExceptionHandler\ExceptionMappingResolver;
use App\Model\ErrorDebugDetails;
use App\Model\ErrorResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListener
{
    public function __construct(
        private readonly ExceptionMappingResolver $resolver,
        private readonly LoggerInterface $logger,
        private readonly SerializerInterface $serializer,
        private readonly bool $isDebug)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if ($this->isSecurityException($throwable)) {
            return;
        }

        $mapping = $this->resolver->resolve($throwable::class);
        if (null === $mapping) {
            $mapping = ExceptionMapping::fromCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if ($mapping->getCode() >= Response::HTTP_INTERNAL_SERVER_ERROR || $mapping->isLoggable()) {
            $this->logger->error($throwable->getMessage(), [
                'trace' => $throwable->getTraceAsString(),
                'previous' => null !== $throwable->getPrevious() ? $throwable->getPrevious()->getMessage() : '',
            ]);
        }

        $message = $mapping->isHidden() ? Response::$statusTexts[$mapping->getCode()] : $throwable->getMessage();
        // Describe details
        $details = $this->isDebug ? new ErrorDebugDetails($throwable->getTraceAsString()) : null;
        $data = $this->serializer->serialize(new ErrorResponse($message, $details), JsonEncoder::FORMAT);

        $event->setResponse(new JsonResponse($data, $mapping->getCode(), [], true));
    }

    private function isSecurityException(\Throwable $throwable): bool
    {
        return $throwable instanceof AuthenticationException;
    }
}
