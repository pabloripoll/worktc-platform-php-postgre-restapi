<?php

declare(strict_types=1);

namespace App\Domain\WorkEntry\Service;

use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\WorkEntry\Repository\WorkEntryRepositoryInterface;

final readonly class WorkEntryService
{
    public function __construct(
        private WorkEntryRepositoryInterface $workEntryRepository
    ) {}

    public function hasActiveEntry(Uuid $userId): bool
    {
        $activeEntry = $this->workEntryRepository->findActiveByUserId($userId);
        return $activeEntry !== null;
    }

    public function calculateTotalHours(Uuid $userId, \DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        $entries = $this->workEntryRepository->findByUserIdAndDateRange($userId, $from, $to);

        $totalMinutes = 0;
        foreach ($entries as $entry) {
            $totalMinutes += $entry->getDurationInMinutes() ?? 0;
        }

        return (int)floor($totalMinutes / 60);
    }
}
