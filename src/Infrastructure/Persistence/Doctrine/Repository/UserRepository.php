<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserRole;
use Doctrine\ORM\EntityManagerInterface;

final class UserRepository implements UserRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function findById(Uuid $id): ?User
    {
        return $this->em->find(User::class, (string)$id);
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => (string)$email]);
    }

    public function findByEmailAndRole(Email $email, UserRole $role): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy([
            'email' => (string)$email,
            'role' => $role->value,
        ]);
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->em->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', (string)$email)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function delete(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * Find users by role with pagination
     *
     * @param UserRole $role The role to filter by
     * @param int $page Page number (1-indexed)
     * @param int $limit Number of items per page
     * @return array{data: User[], total: int, page: int, limit: int}
     */
    public function findByRolePaginated(UserRole $role, int $page, int $limit): array
    {
        // Calculate offset from page number
        $offset = ($page - 1) * $limit;

        // Get paginated users
        $users = $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.role = :role')
            ->andWhere('u.deletedAt IS NULL')
            ->setParameter('role', $role->value)
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        // Get total count
        $total = $this->countByRole($role);

        return [
            'data' => $users,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    public function countByRole(UserRole $role): int
    {
        return (int)$this->em->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->where('u.role = :role')
            ->andWhere('u.deletedAt IS NULL')
            ->setParameter('role', $role->value)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
