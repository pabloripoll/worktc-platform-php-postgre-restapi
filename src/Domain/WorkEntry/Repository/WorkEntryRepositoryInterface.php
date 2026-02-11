<?php

declare(strict_types=1);

namespace App\Domain\WorkEntry\Repository;

use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\WorkEntry\Entity\WorkEntry;

interface WorkEntryRepositoryInterface
{
    public function save(WorkEntry $workEntry): void;
    public function findById(Uuid $id): ?WorkEntry;

    /**
     * @return WorkEntry[]
     */
    public function findByUserId(Uuid $userId): array;

    public function findActiveByUserId(Uuid $userId): ?WorkEntry;

    /**
     * @return WorkEntry[]
     */
    public function findByUserIdAndDateRange(Uuid $userId, \DateTimeImmutable $from, \DateTimeImmutable $to): array;

    public function delete(WorkEntry $workEntry): void;
}
