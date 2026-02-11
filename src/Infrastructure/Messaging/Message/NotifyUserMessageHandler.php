<?php

namespace App\Message;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\NotifyUserMessage;

#[AsMessageHandler]
final class NotifyUserMessageHandler
{
    public function __construct(private LoggerInterface $logger) {}

    // invokable handler - Messenger will call this
    public function __invoke(NotifyUserMessage $message): void
    {
        // Example handling logic — replace with your real email/push logic
        $this->logger->info('Notify user', [
            'userId' => $message->userId,
            'text' => $message->text,
        ]);
    }
}
