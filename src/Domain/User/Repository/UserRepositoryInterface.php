<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserRole;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(Uuid $id): ?User;

    public function findByEmail(Email $email): ?User;

    public function findByEmailAndRole(Email $email, UserRole $role): ?User;

    public function existsByEmail(Email $email): bool;

    /**
     * Find users by role with pagination
     *
     * @param UserRole $role The role to filter by
     * @param int $page Page number (1-indexed)
     * @param int $limit Number of items per page
     * @return array{data: User[], total: int, page: int, limit: int}
     */
    public function findByRolePaginated(UserRole $role, int $page, int $limit): array;

    public function countByRole(UserRole $role): int;

    public function delete(User $user): void;
}
