<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UpdateMemberProfileHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(UpdateMemberProfileCommand $command): void
    {
        $userId = Uuid::fromString($command->userId);
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new DomainException('User not found');
        }

        if (!$user->isMember()) {
            throw new DomainException('User is not a member');
        }

        // Update name
        if ($command->name !== null) {
            $user->updateName($command->name);
        }

        // Update surname
        if ($command->surname !== null) {
            $user->updateSurname($command->surname);
        }

        // Update phone number
        if ($command->phoneNumber !== null) {
            $user->updatePhoneNumber($command->phoneNumber);
        }

        // Update department
        if ($command->department !== null) {
            $user->updateDepartment($command->department);
        }

        // Update birth date
        if ($command->birthDate !== null) {
            $user->updateBirthDate($command->birthDate);
        }

        // Update password (requires current password verification)
        if ($command->newPassword !== null) {
            if ($command->currentPassword === null) {
                throw new DomainException('Current password is required to change password');
            }

            if (!$this->passwordHasher->isPasswordValid($user, $command->currentPassword)) {
                throw new DomainException('Current password is incorrect');
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, $command->newPassword);
            $user->updatePassword($hashedPassword);
        }

        // Save the user
        $this->userRepository->save($user);
    }
}
