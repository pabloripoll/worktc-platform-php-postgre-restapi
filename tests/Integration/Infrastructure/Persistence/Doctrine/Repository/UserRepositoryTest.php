<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserRole;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class UserRepositoryTest extends KernelTestCase
{
    private UserRepositoryInterface $repository;
    private Uuid $adminId;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->repository = $container->get(UserRepositoryInterface::class);

        // Create an admin with unique email for each test
        $admin = User::createAdmin(
            Uuid::random(),
            Email::fromString('admin_' . uniqid() . '@example.com'),  // Unique per test
            'password',
            Uuid::random()
        );
        $this->repository->save($admin);
        $this->adminId = $admin->getId();
    }

    public function testSaveAndFindById(): void
    {
        $userId = Uuid::random();
        $email = Email::fromString('test_' . uniqid() . '@example.com');

        $user = User::createMember($userId, $email, 'password', $this->adminId);
        $this->repository->save($user);

        $foundUser = $this->repository->findById($userId);

        $this->assertNotNull($foundUser);
        $this->assertEquals((string)$userId, (string)$foundUser->getId());
        $this->assertEquals((string)$email, (string)$foundUser->getEmail());
    }

    public function testFindByEmail(): void
    {
        $email = Email::fromString('findme_' . uniqid() . '@example.com');
        $user = User::createMember(Uuid::random(), $email, 'password', $this->adminId);
        $this->repository->save($user);

        $foundUser = $this->repository->findByEmail($email);

        $this->assertNotNull($foundUser);
        $this->assertEquals((string)$email, (string)$foundUser->getEmail());
    }

    public function testExistsByEmail(): void
    {
        $email = Email::fromString('exists_' . uniqid() . '@example.com');
        $user = User::createMember(Uuid::random(), $email, 'password', $this->adminId);
        $this->repository->save($user);

        $this->assertTrue($this->repository->existsByEmail($email));
        $this->assertFalse($this->repository->existsByEmail(Email::fromString('notexists@example.com')));
    }

    public function testFindByEmailAndRole(): void
    {
        $email = Email::fromString('member_' . uniqid() . '@example.com');
        $user = User::createMember(Uuid::random(), $email, 'password', $this->adminId);
        $this->repository->save($user);

        $foundUser = $this->repository->findByEmailAndRole($email, UserRole::MEMBER);
        $this->assertNotNull($foundUser);

        $notFoundUser = $this->repository->findByEmailAndRole($email, UserRole::ADMIN);
        $this->assertNull($notFoundUser);
    }

    public function testSoftDelete(): void
    {
        $userId = Uuid::random();
        $user = User::createMember(
            $userId,
            Email::fromString('delete_' . uniqid() . '@example.com'),
            'password',
            $this->adminId
        );
        $this->repository->save($user);

        $user->softDelete();
        $this->repository->save($user);

        $result = $this->repository->findByRolePaginated(UserRole::MEMBER, 1, 10);

        // Should not include soft-deleted users
        $ids = array_map(fn($u) => (string)$u->getId(), $result['data']);
        $this->assertNotContains((string)$userId, $ids);
    }

    public function testCountByRole(): void
    {
        $initialCount = $this->repository->countByRole(UserRole::MEMBER);

        // Create 3 members
        for ($i = 0; $i < 3; $i++) {
            $user = User::createMember(
                Uuid::random(),
                Email::fromString('countmember_' . $i . '_' . uniqid() . '@example.com'),
                'password',
                $this->adminId
            );
            $this->repository->save($user);
        }

        $newCount = $this->repository->countByRole(UserRole::MEMBER);
        $this->assertEquals($initialCount + 3, $newCount);
    }

    public function testPaginationWorks(): void
    {
        $initialCount = $this->repository->countByRole(UserRole::MEMBER);

        // Create 5 members
        for ($i = 1; $i <= 5; $i++) {
            $member = User::createMember(
                Uuid::random(),
                Email::fromString("paginmember_{$i}_" . uniqid() . "@example.com"),
                'password',
                $this->adminId
            );
            $member->updateName("Member{$i}");
            $member->updateSurname("Test");
            $this->repository->save($member);
        }

        // Test first page (2 items per page)
        $page1 = $this->repository->findByRolePaginated(UserRole::MEMBER, 1, 2);

        $this->assertCount(2, $page1['data'], 'Page 1 should have 2 items');
        $this->assertEquals($initialCount + 5, $page1['total'], 'Total should be initial count + 5');
        $this->assertEquals(1, $page1['page']);
        $this->assertEquals(2, $page1['limit']);

        // Test second page
        $page2 = $this->repository->findByRolePaginated(UserRole::MEMBER, 2, 2);

        $this->assertCount(2, $page2['data'], 'Page 2 should have 2 items');
        $this->assertEquals($initialCount + 5, $page2['total'], 'Total should be consistent');
        $this->assertEquals(2, $page2['page']);

        // Test third page (should have at least 1, might have more from fixtures)
        $page3 = $this->repository->findByRolePaginated(UserRole::MEMBER, 3, 2);

        $this->assertGreaterThanOrEqual(1, count($page3['data']), 'Page 3 should have at least 1 item');

        // Verify no duplicates across pages
        $page1Ids = array_map(fn($u) => (string)$u->getId(), $page1['data']);
        $page2Ids = array_map(fn($u) => (string)$u->getId(), $page2['data']);

        $duplicates = array_intersect($page1Ids, $page2Ids);
        $this->assertEmpty($duplicates, 'Pages should not have duplicate items');
    }

    public function testDelete(): void
    {
        $userId = Uuid::random();
        $user = User::createMember(
            $userId,
            Email::fromString('harddelete_' . uniqid() . '@example.com'),
            'password',
            $this->adminId
        );
        $this->repository->save($user);

        $this->repository->delete($user);

        $foundUser = $this->repository->findById($userId);
        $this->assertNull($foundUser);
    }
}
