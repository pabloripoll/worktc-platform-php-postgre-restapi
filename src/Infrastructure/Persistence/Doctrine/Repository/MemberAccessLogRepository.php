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
        ], ['createdAt' => 'DESC']);
    }

    public function findByUserId(Uuid $userId): array
    {
        return $this->em->getRepository(MemberAccessLog::class)->findBy(
            ['userId' => (string)$userId],
            ['createdAt' => 'DESC']
        );
    }

    public function findAll(): array
    {
        return $this->em->getRepository(MemberAccessLog::class)->findBy(
            [],
            ['createdAt' => 'DESC']
        );
    }

    public function findAllActive(): array
    {
        return $this->em->getRepository(MemberAccessLog::class)->findBy([
            'isTerminated' => false,
            'isExpired' => false,
        ], ['createdAt' => 'DESC']);
    }

    public function terminateAllByUserId(Uuid $userId): void
    {
        $this->em->createQueryBuilder()
            ->update(MemberAccessLog::class, 'm')
            ->set('m.isTerminated', ':terminated')
            ->set('m.updatedAt', ':now')
            ->where('m.userId = :userId')
            ->andWhere('m.isTerminated = :notTerminated')
            ->setParameter('terminated', true)
            ->setParameter('notTerminated', false)
            ->setParameter('userId', (string)$userId)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
