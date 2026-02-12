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
    public function delete(User $user): void;

    /**
     * @return User[]
     */
    public function findByRolePaginated(UserRole $role, int $limit, int $offset): array;

    public function countByRole(UserRole $role): int;
}
