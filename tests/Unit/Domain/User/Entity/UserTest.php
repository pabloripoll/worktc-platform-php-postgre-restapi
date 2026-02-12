<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\User\Entity;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserRole;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testCreateAdmin(): void
    {
        $userId = Uuid::random();
        $email = Email::fromString('admin@example.com');
        $password = 'password123';
        $createdByUserId = Uuid::random();

        $user = User::createAdmin($userId, $email, $password, $createdByUserId);

        $this->assertEquals($userId, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isMember());
    }

    public function testCreateMember(): void
    {
        $userId = Uuid::random();
        $email = Email::fromString('member@example.com');
        $password = 'password123';
        $createdByUserId = Uuid::random();

        $user = User::createMember($userId, $email, $password, $createdByUserId);

        $this->assertEquals($userId, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertTrue($user->isMember());
        $this->assertFalse($user->isAdmin());
    }

    public function testUserIsAdmin(): void
    {
        $user = User::createAdmin(
            Uuid::random(),
            Email::fromString('admin@example.com'),
            'password',
            Uuid::random()
        );

        $this->assertTrue($user->isAdmin());
    }

    public function testUserIsMember(): void
    {
        $user = User::createMember(
            Uuid::random(),
            Email::fromString('member@example.com'),
            'password',
            Uuid::random()
        );

        $this->assertTrue($user->isMember());
    }

    public function testUpdatePassword(): void
    {
        $user = User::createMember(
            Uuid::random(),
            Email::fromString('member@example.com'),
            'password',
            Uuid::random()
        );

        $newPassword = 'newHashedPassword';
        $user->updatePassword($newPassword);

        $this->assertEquals($newPassword, $user->getPassword());
    }

    public function testSoftDelete(): void
    {
        $user = User::createMember(
            Uuid::random(),
            Email::fromString('member@example.com'),
            'password',
            Uuid::random()
        );

        $this->assertNull($user->getDeletedAt());

        $user->softDelete();

        $this->assertNotNull($user->getDeletedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getDeletedAt());
    }

    public function testGetUserIdentifier(): void
    {
        $email = 'test@example.com';
        $user = User::createMember(
            Uuid::random(),
            Email::fromString($email),
            'password',
            Uuid::random()
        );

        $this->assertEquals($email, $user->getUserIdentifier());
    }

    public function testGetRolesForAdmin(): void
    {
        $user = User::createAdmin(
            Uuid::random(),
            Email::fromString('admin@example.com'),
            'password',
            Uuid::random()
        );

        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
    }

    public function testGetRolesForMember(): void
    {
        $user = User::createMember(
            Uuid::random(),
            Email::fromString('member@example.com'),
            'password',
            Uuid::random()
        );

        $roles = $user->getRoles();

        $this->assertContains('ROLE_MEMBER', $roles);
    }

    public function testEraseCredentials(): void
    {
        $user = User::createMember(
            Uuid::random(),
            Email::fromString('member@example.com'),
            'password',
            Uuid::random()
        );

        // Should not throw exception
        $user->eraseCredentials();

        $this->assertTrue(true);
    }

    public function testCreatedAtIsSetOnCreation(): void
    {
        $user = User::createMember(
            Uuid::random(),
            Email::fromString('member@example.com'),
            'password',
            Uuid::random()
        );

        $this->assertNotNull($user->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    public function testUpdatedAtChangesOnPasswordChange(): void
    {
        $user = User::createMember(
            Uuid::random(),
            Email::fromString('member@example.com'),
            'password',
            Uuid::random()
        );

        $originalUpdatedAt = $user->getUpdatedAt();

        // Small delay to ensure different timestamp
        usleep(1000);

        $user->updatePassword('newHashedPassword');

        $this->assertNotEquals($originalUpdatedAt, $user->getUpdatedAt());
    }

    public function testUpdatedAtChangesOnSoftDelete(): void
    {
        $user = User::createMember(
            Uuid::random(),
            Email::fromString('member@example.com'),
            'password',
            Uuid::random()
        );

        $originalUpdatedAt = $user->getUpdatedAt();

        // Small delay to ensure different timestamp
        usleep(1000);

        $user->softDelete();

        $this->assertNotEquals($originalUpdatedAt, $user->getUpdatedAt());
    }
}
