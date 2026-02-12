<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Command;

final readonly class DeleteWorkEntryCommand
{
    public function __construct(
        public string $workEntryId,
        public string $userId, // For authorization check
        public ?string $deletedByUserId = null
    ) {}
}
