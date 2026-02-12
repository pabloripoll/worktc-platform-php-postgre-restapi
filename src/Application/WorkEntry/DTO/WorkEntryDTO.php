<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\DTO;

final readonly class WorkEntryDTO
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $startDate,
        public ?string $endDate,
        public string $createdAt,
        public string $updatedAt,
        public ?string $deletedAt,
        public ?int $durationMinutes
    ) {}
}
