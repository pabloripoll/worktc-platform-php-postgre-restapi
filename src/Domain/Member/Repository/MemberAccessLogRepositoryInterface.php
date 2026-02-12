<?php

declare(strict_types=1);

namespace App\Domain\Member\Repository;

use App\Domain\Member\Entity\MemberAccessLog;
use App\Domain\Shared\ValueObject\Uuid;

interface MemberAccessLogRepositoryInterface
{
    public function save(MemberAccessLog $accessLog): void;
    public function findById(int $id): ?MemberAccessLog;
    public function findByToken(string $token): ?MemberAccessLog;

    /**
     * @return MemberAccessLog[]
     */
    public function findActiveByUserId(Uuid $userId): array;

    /**
     * @return MemberAccessLog[]
     */
    public function findByUserId(Uuid $userId): array;

    /**
     * @return MemberAccessLog[]
     */
    public function findAll(): array;

    /**
     * @return MemberAccessLog[]
     */
    public function findAllActive(): array;

    public function terminateAllByUserId(Uuid $userId): void;
}
