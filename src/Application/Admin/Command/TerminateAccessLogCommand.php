<?php

declare(strict_types=1);

namespace App\Application\Admin\Command;

final readonly class TerminateAccessLogCommand
{
    public function __construct(
        public int $accessLogId,
        public string $userId // For authorization check
    ) {}
}
