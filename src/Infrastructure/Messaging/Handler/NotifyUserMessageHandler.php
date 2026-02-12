<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging\Handler;

use App\Infrastructure\Messaging\Message\NotifyUserMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class NotifyUserMessageHandler
{
    public function __construct(
        private LoggerInterface $logger
    ) {}

    public function __invoke(NotifyUserMessage $message): void
    {
        $this->logger->info('Processing notification message', [
            'user_id' => $message->userId,
            'message' => $message->message,
        ]);

        // Simulate some processing
        // In real scenario: send email, push notification, etc.

        $this->logger->info('Notification processed successfully', [
            'user_id' => $message->userId,
        ]);
    }
}
