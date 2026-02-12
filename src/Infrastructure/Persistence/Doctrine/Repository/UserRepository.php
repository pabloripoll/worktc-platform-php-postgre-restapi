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
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Persist and flush a user entity to the database
     *
     * @param User $user The user entity to save
     * @return void
     */
    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();

        // Note: We don't detach here anymore as it breaks integration tests
        // and the $em->clear() in GetUserByIdHandler handles cache invalidation
    }

    /**
     * Find a user by their unique identifier
     *
     * @param Uuid $id The user's unique identifier
     * @return User|null The user entity or null if not found
     */
    public function findById(Uuid $id): ?User
    {
        // Use DQL query instead of find() to bypass identity map cache
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.id = :id')
            ->setParameter('id', (string)$id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find a user by their email address
     *
     * @param Email $email The user's email address
     * @return User|null The user entity or null if not found
     */
    public function findByEmail(Email $email): ?User
    {
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', (string)$email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find a user by their email address and role
     *
     * @param Email $email The user's email address
     * @param UserRole $role The user's role
     * @return User|null The user entity or null if not found
     */
    public function findByEmailAndRole(Email $email, UserRole $role): ?User
    {
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->andWhere('u.role = :role')
            ->setParameter('email', (string)$email)
            ->setParameter('role', $role)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Check if a user exists with the given email address
     *
     * @param Email $email The email address to check
     * @return bool True if a user exists, false otherwise
     */
    public function existsByEmail(Email $email): bool
    {
        $count = $this->em->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', (string)$email)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    /**
     * Find users by role with pagination
     *
     * @param UserRole $role The role to filter by
     * @param int $page Page number (1-indexed)
     * @param int $limit Number of items per page
     * @return array{data: User[], total: int, page: int, limit: int} Paginated user results
     */
    public function findByRolePaginated(UserRole $role, int $page, int $limit): array
    {
        $offset = ($page - 1) * $limit;

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

        $total = $this->countByRole($role);

        return [
            'data' => $users,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * Count the number of users with a specific role
     *
     * @param UserRole $role The role to count
     * @return int The number of users with the given role
     */
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

    /**
     * Remove a user from the database
     *
     * @param User $user The user entity to delete
     * @return void
     */
    public function delete(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}
