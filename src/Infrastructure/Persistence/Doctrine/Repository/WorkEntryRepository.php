<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class WorkEntryRepository implements WorkEntryRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(WorkEntry $workEntry): void
    {
        $this->em->persist($workEntry);
        $this->em->flush();
    }

    public function findById(Uuid $id): ?WorkEntry
    {
        return $this->em->find(WorkEntry::class, (string)$id);
    }

    public function findByUserId(Uuid $userId): array
    {
        return $this->em->getRepository(WorkEntry::class)->findBy(
            ['userId' => (string)$userId, 'deletedAt' => null],
            ['startDate' => 'DESC']
        );
    }

    public function findActiveByUserId(Uuid $userId): ?WorkEntry
    {
        return $this->em->getRepository(WorkEntry::class)->findOneBy([
            'userId' => (string)$userId,
            'endDate' => null,
            'deletedAt' => null,
        ]);
    }

    public function findByUserIdAndDateRange(Uuid $userId, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->em->createQueryBuilder()
            ->select('w')
            ->from(WorkEntry::class, 'w')
            ->where('w.userId = :userId')
            ->andWhere('w.startDate >= :from')
            ->andWhere('w.startDate <= :to')
            ->andWhere('w.deletedAt IS NULL')
            ->setParameter('userId', (string)$userId)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('w.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function delete(WorkEntry $workEntry): void
    {
        $this->em->remove($workEntry);
        $this->em->flush();
    }
}
