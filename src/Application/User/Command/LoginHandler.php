<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Domain\Admin\Entity\AdminAccessLog;
use App\Domain\Admin\Repository\AdminAccessLogRepositoryInterface;
use App\Domain\Member\Entity\MemberAccessLog;
use App\Domain\Member\Repository\MemberAccessLogRepositoryInterface;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserRole;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class LoginHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AdminAccessLogRepositoryInterface $adminAccessLogRepository,
        private MemberAccessLogRepositoryInterface $memberAccessLogRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
        private int $jwtTtl = 3600 // From config
    ) {}

    /**
     * @return array{token: string, expires_in: int, user_id: string}
     */
    public function __invoke(LoginCommand $command): array
    {
        $email = Email::fromString($command->email);
        $role = UserRole::from($command->role);

        // Find user by email and role
        $user = $this->userRepository->findByEmailAndRole($email, $role);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $command->password)) {
            throw new ValidationException('Invalid credentials');
        }

        if ($user->isDeleted()) {
            throw new ValidationException('User account is deleted');
        }

        // Generate JWT
        $token = $this->jwtManager->create($user);
        $expiresAt = (new \DateTimeImmutable())->modify("+{$this->jwtTtl} seconds");

        // Log access
        if ($role === UserRole::ADMIN) {
            $accessLog = AdminAccessLog::create(
                $user->getId(),
                $token,
                $expiresAt,
                $command->ipAddress,
                $command->userAgent
            );
            $this->adminAccessLogRepository->save($accessLog);
        } else {
            $accessLog = MemberAccessLog::create(
                $user->getId(),
                $token,
                $expiresAt,
                $command->ipAddress,
                $command->userAgent
            );
            $this->memberAccessLogRepository->save($accessLog);
        }

        return [
            'token' => $token,
            'expires_in' => $this->jwtTtl,
            'user_id' => (string)$user->getId(),
        ];
    }
}
