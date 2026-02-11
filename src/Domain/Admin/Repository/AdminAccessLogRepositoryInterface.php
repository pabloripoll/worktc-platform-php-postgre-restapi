<?php

declare(strict_types=1);

namespace App\Domain\Admin\Repository;

use App\Domain\Admin\Entity\AdminAccessLog;
use App\Domain\Shared\ValueObject\Uuid;

interface AdminAccessLogRepositoryInterface
{
    public function save(AdminAccessLog $accessLog): void;
    public function findById(int $id): ?AdminAccessLog;
    public function findByToken(string $token): ?AdminAccessLog;

    /**
     * @return AdminAccessLog[]
     */
    public function findActiveByUserId(Uuid $userId): array;

    public function terminateAllByUserId(Uuid $userId): void;
}
