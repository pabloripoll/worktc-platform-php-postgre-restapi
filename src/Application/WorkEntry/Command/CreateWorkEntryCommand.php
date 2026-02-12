<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Command;

final readonly class CreateWorkEntryCommand
{
    public function __construct(
        public string $userId,
        public string $startDate,
        public ?string $endDate = null,
        public ?string $createdByUserId = null
    ) {}
}
