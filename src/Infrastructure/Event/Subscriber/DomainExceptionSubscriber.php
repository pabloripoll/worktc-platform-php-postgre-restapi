<?php

declare(strict_types=1);

namespace App\Infrastructure\Event\Subscriber;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class DomainExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Only handle domain exceptions
        if (!$exception instanceof DomainException) {
            return;
        }

        $statusCode = match (true) {
            $exception instanceof EntityNotFoundException => JsonResponse::HTTP_NOT_FOUND,
            $exception instanceof ValidationException => JsonResponse::HTTP_BAD_REQUEST,
            default => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
        };

        $response = new JsonResponse([
            'error' => $exception->getMessage(),
            'type' => $this->getExceptionType($exception),
        ], $statusCode);

        $event->setResponse($response);
    }

    private function getExceptionType(\Throwable $exception): string
    {
        $className = get_class($exception);
        $parts = explode('\\', $className);
        return end($parts);
    }
}
