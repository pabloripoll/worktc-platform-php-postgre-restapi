<?php

namespace App\Infrastructure\Messaging\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(['async'])]
class NotifyUserMessage
{
    public function __construct(
        public int $userId,
        public string $text
    ) {}
}
