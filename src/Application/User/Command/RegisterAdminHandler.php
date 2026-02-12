<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Domain\Admin\Entity\AdminProfile;
use App\Domain\Admin\Repository\AdminProfileRepositoryInterface;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class RegisterAdminHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AdminProfileRepositoryInterface $adminProfileRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(RegisterAdminCommand $command): Uuid
    {
        // Validation
        $email = Email::fromString($command->email);

        if ($this->userRepository->existsByEmail($email)) {
            throw new ValidationException('Email already exists');
        }

        // Create User
        $userId = Uuid::generate();
        $createdByUserId = $command->createdByUserId
            ? Uuid::fromString($command->createdByUserId)
            : $userId; // Self-created for first admin

        $tempUser = User::createAdmin($userId, $email, 'temp', $createdByUserId);
        $hashedPassword = $this->passwordHasher->hashPassword($tempUser, $command->password);

        $user = User::createAdmin($userId, $email, $hashedPassword, $createdByUserId);
        $this->userRepository->save($user);

        // Create Admin Profile
        $birthDate = $command->birthDate ? new \DateTimeImmutable($command->birthDate) : null;

        $adminProfile = AdminProfile::create(
            $userId,
            $command->name,
            $command->surname,
            $birthDate,
            $command->phoneNumber,
            $command->department
        );
        $this->adminProfileRepository->save($adminProfile);

        return $userId;
    }
}
