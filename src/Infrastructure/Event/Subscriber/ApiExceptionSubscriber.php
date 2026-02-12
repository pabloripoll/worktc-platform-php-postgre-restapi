<?php

declare(strict_types=1);

namespace App\Infrastructure\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Log\LoggerInterface;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private bool $debug = false,
        private ?LoggerInterface $logger = null
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Only handle API requests
        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        // Log the exception
        $this->logger?->error('API Exception', [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        $data = [
            'status' => $statusCode,
            'error' => $this->getErrorType($exception),
            'message' => $this->debug ? $exception->getMessage() : 'Internal server error.',
        ];

        if ($this->debug) {
            $data['trace'] = explode("\n", $exception->getTraceAsString());
            $data['file'] = $exception->getFile();
            $data['line'] = $exception->getLine();
        }

        $response = new JsonResponse($data, $statusCode);

        if ($exception instanceof HttpExceptionInterface) {
            $response->headers->replace($exception->getHeaders());
        }

        $event->setResponse($response);
    }

    private function getErrorType(\Throwable $exception): string
    {
        $className = get_class($exception);
        $parts = explode('\\', $className);
        $shortName = end($parts);

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Exception', '', $shortName)));
    }
}
