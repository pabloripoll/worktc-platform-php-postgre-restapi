<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Member\Entity\MemberProfile;
use App\Domain\Member\Repository\MemberProfileRepositoryInterface;
use App\Domain\Shared\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

final class MemberProfileRepository implements MemberProfileRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(MemberProfile $profile): void
    {
        $this->em->persist($profile);
        $this->em->flush();
    }

    public function findByUserId(Uuid $userId): ?MemberProfile
    {
        return $this->em->find(MemberProfile::class, (string)$userId);
    }

    public function delete(MemberProfile $profile): void
    {
        $this->em->remove($profile);
        $this->em->flush();
    }
}
