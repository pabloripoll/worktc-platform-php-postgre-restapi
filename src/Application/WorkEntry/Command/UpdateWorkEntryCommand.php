<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Command;

final readonly class UpdateWorkEntryCommand
{
    public function __construct(
        public string $workEntryId,
        public string $userId, // For authorization check
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?string $updatedByUserId = null
    ) {}
}
