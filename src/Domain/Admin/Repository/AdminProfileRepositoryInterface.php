<?php

declare(strict_types=1);

namespace App\Domain\Admin\Repository;

use App\Domain\Admin\Entity\AdminProfile;
use App\Domain\Shared\ValueObject\Uuid;

interface AdminProfileRepositoryInterface
{
    public function save(AdminProfile $profile): void;
    public function findByUserId(Uuid $userId): ?AdminProfile;
    public function delete(AdminProfile $profile): void;
}
