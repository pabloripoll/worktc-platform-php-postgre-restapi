<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Admin\Entity\AdminAccessLog;
use App\Domain\Admin\Repository\AdminAccessLogRepositoryInterface;
use App\Domain\Shared\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

final class AdminAccessLogRepository implements AdminAccessLogRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(AdminAccessLog $accessLog): void
    {
        $this->em->persist($accessLog);
        $this->em->flush();
    }

    public function findById(int $id): ?AdminAccessLog
    {
        return $this->em->find(AdminAccessLog::class, $id);
    }

    public function findByToken(string $token): ?AdminAccessLog
    {
        return $this->em->getRepository(AdminAccessLog::class)->findOneBy(['token' => $token]);
    }

    public function findActiveByUserId(Uuid $userId): array
    {
        return $this->em->getRepository(AdminAccessLog::class)->findBy([
            'userId' => (string)$userId,
            'isTerminated' => false,
            'isExpired' => false,
        ]);
    }

    public function terminateAllByUserId(Uuid $userId): void
    {
        $this->em->createQueryBuilder()
            ->update(AdminAccessLog::class, 'a')
            ->set('a.isTerminated', ':terminated')
            ->set('a.updatedAt', ':now')
            ->where('a.userId = :userId')
            ->andWhere('a.isTerminated = :notTerminated')
            ->setParameter('terminated', true)
            ->setParameter('notTerminated', false)
            ->setParameter('userId', (string)$userId)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
