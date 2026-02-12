<?php

declare(strict_types=1);

namespace App\Domain\Member\Service;

use App\Domain\Member\Repository\MemberAccessLogRepositoryInterface;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class MemberAuthenticationService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private MemberAccessLogRepositoryInterface $accessLogRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function authenticate(Email $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$user->isMember()) {
            return false;
        }

        return $this->passwordHasher->isPasswordValid($user, $password);
    }
}
