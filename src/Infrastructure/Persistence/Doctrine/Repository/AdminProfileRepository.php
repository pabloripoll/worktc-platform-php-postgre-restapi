<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Admin\Entity\AdminProfile;
use App\Domain\Admin\Repository\AdminProfileRepositoryInterface;
use App\Domain\Shared\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

final class AdminProfileRepository implements AdminProfileRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(AdminProfile $profile): void
    {
        $this->em->persist($profile);
        $this->em->flush();
    }

    public function findByUserId(Uuid $userId): ?AdminProfile
    {
        return $this->em->find(AdminProfile::class, (string)$userId);
    }

    public function delete(AdminProfile $profile): void
    {
        $this->em->remove($profile);
        $this->em->flush();
    }
}
