<?php

declare(strict_types=1);

namespace App\Domain\Admin\Service;

use App\Domain\Admin\Repository\AdminAccessLogRepositoryInterface;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class AdminAuthenticationService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AdminAccessLogRepositoryInterface $accessLogRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function authenticate(Email $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$user->isAdmin()) {
            return false;
        }

        return $this->passwordHasher->isPasswordValid($user, $password);
    }
}
