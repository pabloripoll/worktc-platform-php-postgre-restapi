<?php

declare(strict_types=1);

namespace App\Domain\Member\Repository;

use App\Domain\Member\Entity\MemberProfile;
use App\Domain\Shared\ValueObject\Uuid;

interface MemberProfileRepositoryInterface
{
    public function save(MemberProfile $profile): void;
    public function findByUserId(Uuid $userId): ?MemberProfile;
    public function delete(MemberProfile $profile): void;
}
