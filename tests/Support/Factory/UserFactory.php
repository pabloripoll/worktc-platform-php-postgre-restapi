<?php

declare(strict_types=1);

namespace App\Tests\Support\Factory;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;

final class UserFactory
{
    public static function createMember(
        ?string $email = null,
        ?string $password = null,
        ?Uuid $userId = null,
        ?Uuid $createdBy = null
    ): User {
        return User::createMember(
            $userId ?? Uuid::generate(),
            Email::fromString($email ?? 'member' . uniqid() . '@example.com'),
            $password ?? 'hashed_password_123',
            $createdBy ?? Uuid::generate()
        );
    }

    public static function createAdmin(
        ?string $email = null,
        ?string $password = null,
        ?Uuid $userId = null,
        ?Uuid $createdBy = null
    ): User {
        return User::createAdmin(
            $userId ?? Uuid::generate(),
            Email::fromString($email ?? 'admin' . uniqid() . '@example.com'),
            $password ?? 'hashed_password_123',
            $createdBy ?? Uuid::generate()
        );
    }
}
