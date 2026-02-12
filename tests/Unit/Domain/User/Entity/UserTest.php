<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\User\Entity;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObject\UserRole;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    private Uuid $creatorId;

    protected function setUp(): void
    {
        $this->creatorId = Uuid::generate();
    }

    public function testCreateAdmin(): void
    {
        $id = Uuid::generate();
        $email = Email::fromString('admin@example.com');
        $hashedPassword = 'hashed_password';

        $user = User::createAdmin($id, $email, $hashedPassword, $this->creatorId);

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals(UserRole::ADMIN, $user->getRole());
        $this->assertEquals($hashedPassword, $user->getPassword());
        $this->assertEquals($this->creatorId, $user->getCreatedByUserId());
        $this->assertFalse($user->isDeleted());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getUpdatedAt());
    }

    public function testCreateMember(): void
    {
        $id = Uuid::generate();
        $email = Email::fromString('member@example.com');
        $hashedPassword = 'hashed_password';

        $user = User::createMember($id, $email, $hashedPassword, $this->creatorId);

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals(UserRole::MEMBER, $user->getRole());
        $this->assertEquals($hashedPassword, $user->getPassword());
        $this->assertEquals($this->creatorId, $user->getCreatedByUserId());
        $this->assertFalse($user->isDeleted());
    }

    public function testUserIsAdmin(): void
    {
        $user = User::createAdmin(
            Uuid::generate(),
            Email::fromString('admin@example.com'),
            'password',
            $this->creatorId
        );

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isMember());
    }

    public function testUserIsMember(): void
    {
        $user = User::createMember(
            Uuid::generate(),
            Email::fromString('member@example.com'),
            'password',
            $this->creatorId
        );

        $this->assertTrue($user->isMember());
        $this->assertFalse($user->isAdmin());
    }

    public function testChangePassword(): void
    {
        $user = User::createMember(
            Uuid::generate(),
            Email::fromString('test@example.com'),
            'old_password',
            $this->creatorId
        );

        $oldUpdatedAt = $user->getUpdatedAt();

        // Small delay to ensure timestamp difference
        usleep(1000);

        $newPassword = 'new_hashed_password';
        $user->changePassword($newPassword);

        $this->assertEquals($newPassword, $user->getPassword());
        $this->assertGreaterThan($oldUpdatedAt, $user->getUpdatedAt());
    }

    public function testSoftDelete(): void
    {
        $user = User::createMember(
            Uuid::generate(),
            Email::fromString('test@example.com'),
            'password',
            $this->creatorId
        );

        $this->assertFalse($user->isDeleted());
        $this->assertNull($user->getDeletedAt());

        $user->softDelete();

        $this->assertTrue($user->isDeleted());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getDeletedAt());
    }

    public function testGetUserIdentifier(): void
    {
        $email = 'test@example.com';
        $user = User::createMember(
            Uuid::generate(),
            Email::fromString($email),
            'password',
            $this->creatorId
        );

        $this->assertEquals($email, $user->getUserIdentifier());
    }

    public function testGetRolesForAdmin(): void
    {
        $user = User::createAdmin(
            Uuid::generate(),
            Email::fromString('admin@example.com'),
            'password',
            $this->creatorId
        );

        $roles = $user->getRoles();

        $this->assertIsArray($roles);
        $this->assertContains('ROLE_ADMIN', $roles);
    }

    public function testGetRolesForMember(): void
    {
        $user = User::createMember(
            Uuid::generate(),
            Email::fromString('member@example.com'),
            'password',
            $this->creatorId
        );

        $roles = $user->getRoles();

        $this->assertIsArray($roles);
        $this->assertContains('ROLE_MEMBER', $roles);
    }

    public function testEraseCredentials(): void
    {
        $user = User::createMember(
            Uuid::generate(),
            Email::fromString('test@example.com'),
            'password',
            $this->creatorId
        );

        // Should not throw exception
        $user->eraseCredentials();

        // Password should remain (no temporary credentials)
        $this->assertEquals('password', $user->getPassword());
    }

    public function testCreatedAtIsSetOnCreation(): void
    {
        $beforeCreation = new \DateTimeImmutable();

        $user = User::createMember(
            Uuid::generate(),
            Email::fromString('test@example.com'),
            'password',
            $this->creatorId
        );

        $afterCreation = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($beforeCreation, $user->getCreatedAt());
        $this->assertLessThanOrEqual($afterCreation, $user->getCreatedAt());
    }

    public function testUpdatedAtChangesOnPasswordChange(): void
    {
        $user = User::createMember(
            Uuid::generate(),
            Email::fromString('test@example.com'),
            'password',
            $this->creatorId
        );

        $originalUpdatedAt = $user->getUpdatedAt();

        usleep(1000); // Ensure time difference

        $user->changePassword('new_password');

        $this->assertGreaterThan($originalUpdatedAt, $user->getUpdatedAt());
    }

    public function testUpdatedAtChangesOnSoftDelete(): void
    {
        $user = User::createMember(
            Uuid::generate(),
            Email::fromString('test@example.com'),
            'password',
            $this->creatorId
        );

        $originalUpdatedAt = $user->getUpdatedAt();

        usleep(1000); // Ensure time difference

        $user->softDelete();

        $this->assertGreaterThan($originalUpdatedAt, $user->getUpdatedAt());
    }
}
