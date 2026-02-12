<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

final readonly class TerminateAllUserAccessLogsCommand
{
    public function __construct(
        public string $userId,
        public bool $byAdmin = false // If admin is terminating another user's sessions
    ) {}
}
