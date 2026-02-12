<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Domain\Member\Entity\MemberProfile;
use App\Domain\Member\Repository\MemberProfileRepositoryInterface;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class RegisterMemberHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private MemberProfileRepositoryInterface $memberProfileRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function __invoke(RegisterMemberCommand $command): Uuid
    {
        // Validation
        $email = Email::fromString($command->email);

        if ($this->userRepository->existsByEmail($email)) {
            throw new ValidationException('Email already exists');
        }

        // Create User
        $userId = Uuid::generate();
        $createdByUserId = Uuid::fromString($command->createdByUserId);

        $tempUser = User::createMember($userId, $email, 'temp', $createdByUserId);
        $hashedPassword = $this->passwordHasher->hashPassword($tempUser, $command->password);

        $user = User::createMember($userId, $email, $hashedPassword, $createdByUserId);
        $this->userRepository->save($user);

        // Create Member Profile
        $birthDate = $command->birthDate ? new \DateTimeImmutable($command->birthDate) : null;

        $memberProfile = MemberProfile::create(
            $userId,
            $command->name,
            $command->surname,
            $birthDate,
            $command->phoneNumber,
            $command->department
        );
        $this->memberProfileRepository->save($memberProfile);

        return $userId;
    }
}
