<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserRole;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepositoryInterface $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->repository = $container->get(UserRepositoryInterface::class);
    }

    private function generateUniqueEmail(string $prefix = 'test'): string
    {
        return $prefix . '_' . uniqid() . '_' . time() . '@example.com';
    }

    public function testSaveAndFindUser(): void
    {
        $userId = Uuid::generate();
        $systemUserId = Uuid::generate();

        $user = User::createMember(
            $userId,
            Email::fromString($this->generateUniqueEmail('save')),
            'hashed_password_123',
            $systemUserId
        );

        $this->repository->save($user);

        $foundUser = $this->repository->findById($userId);

        $this->assertNotNull($foundUser);
        $this->assertEquals($userId, $foundUser->getId());
        $this->assertTrue($foundUser->isMember());
    }

    public function testFindByEmail(): void
    {
        $userId = Uuid::generate();
        $systemUserId = Uuid::generate();
        $email = Email::fromString($this->generateUniqueEmail('findme'));

        $user = User::createMember(
            $userId,
            $email,
            'hashed_password_123',
            $systemUserId
        );

        $this->repository->save($user);

        $foundUser = $this->repository->findByEmail($email);

        $this->assertNotNull($foundUser);
        $this->assertEquals((string)$email, (string)$foundUser->getEmail());
    }

    public function testExistsByEmail(): void
    {
        $userId = Uuid::generate();
        $systemUserId = Uuid::generate();
        $email = Email::fromString($this->generateUniqueEmail('exists'));

        $user = User::createMember(
            $userId,
            $email,
            'hashed_password_123',
            $systemUserId
        );

        $this->repository->save($user);

        $exists = $this->repository->existsByEmail($email);

        $this->assertTrue($exists);

        $notExists = $this->repository->existsByEmail(
            Email::fromString($this->generateUniqueEmail('notexists'))
        );
        $this->assertFalse($notExists);
    }

    public function testFindByRolePaginated(): void
    {
        $systemUserId = Uuid::generate();

        // Create multiple members with unique emails
        for ($i = 0; $i < 3; $i++) {
            $userId = Uuid::generate();
            $user = User::createMember(
                $userId,
                Email::fromString($this->generateUniqueEmail("member{$i}")),
                'hashed_password_123',
                $systemUserId
            );
            $this->repository->save($user);
        }

        // Create an admin with unique email
        $adminId = Uuid::generate();
        $admin = User::createAdmin(
            $adminId,
            Email::fromString($this->generateUniqueEmail('admin')),
            'hashed_password_123',
            $systemUserId
        );
        $this->repository->save($admin);

        // Find members only
        $result = $this->repository->findByRolePaginated(UserRole::MEMBER, 1, 10);

        // Assertions for the result structure
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('limit', $result);

        // Check that data is an array
        $this->assertIsArray($result['data']);
        $this->assertGreaterThanOrEqual(3, $result['total']);

        // Verify each user in the data array is a member
        foreach ($result['data'] as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertTrue($user->isMember());
        }
    }

    public function testFindAdmins(): void
    {
        $systemUserId = Uuid::generate();

        // Create an admin with unique email
        $adminId = Uuid::generate();
        $admin = User::createAdmin(
            $adminId,
            Email::fromString($this->generateUniqueEmail('testadmin')),
            'hashed_password_123',
            $systemUserId
        );
        $this->repository->save($admin);

        // Find admins
        $result = $this->repository->findByRolePaginated(UserRole::ADMIN, 1, 10);

        // Assertions
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
        $this->assertGreaterThanOrEqual(1, $result['total']);

        // Verify each user is an admin
        foreach ($result['data'] as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertTrue($user->isAdmin());
        }
    }

    public function testSoftDelete(): void
    {
        $userId = Uuid::generate();
        $systemUserId = Uuid::generate();

        $user = User::createMember(
            $userId,
            Email::fromString($this->generateUniqueEmail('todelete')),
            'hashed_password_123',
            $systemUserId
        );

        $this->repository->save($user);

        // Soft delete
        $user->softDelete();
        $this->repository->save($user);

        // Find by ID should still work
        $foundUser = $this->repository->findById($userId);
        $this->assertNotNull($foundUser);
        $this->assertNotNull($foundUser->getDeletedAt());
    }

    public function testFindNonExistentUser(): void
    {
        $nonExistentId = Uuid::generate();
        $foundUser = $this->repository->findById($nonExistentId);

        $this->assertNull($foundUser);
    }

    public function testPaginationWorks(): void
    {
        $systemUserId = Uuid::generate();

        // Create 15 members with unique emails
        for ($i = 0; $i < 15; $i++) {
            $userId = Uuid::generate();
            $user = User::createMember(
                $userId,
                Email::fromString($this->generateUniqueEmail("paginated{$i}")),
                'hashed_password_123',
                $systemUserId
            );
            $this->repository->save($user);
        }

        // Get first page (5 items)
        $page1 = $this->repository->findByRolePaginated(UserRole::MEMBER, 1, 5);
        $this->assertCount(5, $page1['data']);
        $this->assertEquals(1, $page1['page']);
        $this->assertEquals(5, $page1['limit']);
        $this->assertGreaterThanOrEqual(15, $page1['total']);

        // Get second page (5 items)
        $page2 = $this->repository->findByRolePaginated(UserRole::MEMBER, 2, 5);
        $this->assertCount(5, $page2['data']);
        $this->assertEquals(2, $page2['page']);
        $this->assertEquals(5, $page2['limit']);

        // Ensure different results
        $this->assertNotEquals(
            $page1['data'][0]->getId(),
            $page2['data'][0]->getId()
        );
    }
}
