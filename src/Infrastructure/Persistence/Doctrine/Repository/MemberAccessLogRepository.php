<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Member\Entity\MemberAccessLog;
use App\Domain\Member\Repository\MemberAccessLogRepositoryInterface;
use App\Domain\Shared\ValueObject\Uuid;
use Doctrine\ORM\EntityManagerInterface;

final class MemberAccessLogRepository implements MemberAccessLogRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(MemberAccessLog $accessLog): void
    {
        $this->em->persist($accessLog);
        $this->em->flush();
    }

    public function findById(int $id): ?MemberAccessLog
    {
        return $this->em->find(MemberAccessLog::class, $id);
    }

    public function findByToken(string $token): ?MemberAccessLog
    {
        return $this->em->getRepository(MemberAccessLog::class)->findOneBy(['token' => $token]);
    }

    public function findActiveByUserId(Uuid $userId): array
    {
        return $this->em->getRepository(MemberAccessLog::class)->findBy([
            'userId' => (string)$userId,
            'isTerminated' => false,
            'isExpired' => false,
        ]);
    }

    public function terminateAllByUserId(Uuid $userId): void
    {
        $this->em->createQueryBuilder()
            ->update(MemberAccessLog::class, 'a')
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
