<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Admin\Entity\AdminProfile;
use App\Domain\Member\Entity\MemberProfile;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Create system admin (self-created)
        $adminId = Uuid::generate();

        // Create a temporary user instance for password hashing
        $tempAdmin = User::createAdmin(
            $adminId,
            Email::fromString('admin@example.com'),
            'temp',
            $adminId
        );

        $admin = User::createAdmin(
            $adminId,
            Email::fromString('admin@example.com'),
            $this->hasher->hashPassword($tempAdmin, 'password123'),
            $adminId
        );
        $manager->persist($admin);

        // Create admin profile
        $adminProfile = AdminProfile::create(
            $adminId,
            'System',
            'Admin',
            new \DateTimeImmutable('1985-06-15'),
            '+34600000001',
            'IT Department'
        );
        $manager->persist($adminProfile);

        // Create test member
        $memberId = Uuid::generate();

        // Create a temporary user instance for password hashing
        $tempMember = User::createMember(
            $memberId,
            Email::fromString('member@example.com'),
            'temp',
            $adminId  // Created by admin
        );

        $member = User::createMember(
            $memberId,
            Email::fromString('member@example.com'),
            $this->hasher->hashPassword($tempMember, 'password123'),
            $adminId
        );
        $manager->persist($member);

        // Create member profile
        $memberProfile = MemberProfile::create(
            $memberId,
            'John',
            'Doe',
            new \DateTimeImmutable('1990-01-15'),
            '+34600123456',
            'Sales Department'
        );
        $manager->persist($memberProfile);

        $manager->flush();
    }
}
