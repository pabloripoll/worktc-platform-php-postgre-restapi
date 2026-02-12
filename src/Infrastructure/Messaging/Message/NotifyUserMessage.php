<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging\Message;

final readonly class NotifyUserMessage
{
    public function __construct(
        public int|string $userId,
        public string $message
    ) {}
}
