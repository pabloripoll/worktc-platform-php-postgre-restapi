<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UpdateMemberPasswordHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(UpdateMemberPasswordCommand $command): void
    {
        $userId = Uuid::fromString($command->userId);
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new EntityNotFoundException('User not found');
        }

        // Verify current password
        if (!$this->passwordHasher->isPasswordValid($user, $command->currentPassword)) {
            throw new ValidationException('Current password is incorrect');
        }

        // Hash and update new password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $command->newPassword);
        $user->changePassword($hashedPassword);

        $this->userRepository->save($user);
    }
}
