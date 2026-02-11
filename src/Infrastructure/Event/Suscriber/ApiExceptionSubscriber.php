<?php

namespace App\Infrastructure\Event\Suscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // High priority so this runs before other listeners
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 100],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $status = 500;
        $error = 'error';
        $message = 'An error occurred';

        // Authentication (not logged in / bad token)
        if ($exception instanceof AuthenticationException) {
            $status = 401;
            $error = 'unauthenticated';
            $message = $exception->getMessage() ?: 'Unauthenticated.';
        } elseif ($exception instanceof AccessDeniedException) {
            // Authorization (authenticated but missing role)
            $status = 403;
            $error = 'forbidden';
            $message = $exception->getMessage() ?: 'Access denied.';
        } elseif ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();
            $message = $exception->getMessage() ?: $message;
            // Map some common HttpException codes to short errors
            $error = match ($status) {
                404 => 'not_found',
                400 => 'bad_request',
                422 => 'validation_error',
                default => 'error'
            };
        } else {
            // keep default 500 and generic message in production APIs
            $message = 'Internal server error.';
            $error = 'internal_error';
        }

        $payload = [
            'status' => $status,
            'error' => $error,
            'message' => $message,
        ];

        $response = new JsonResponse($payload, $status);

        // Replace Symfony's default response
        $event->setResponse($response);
    }
}
