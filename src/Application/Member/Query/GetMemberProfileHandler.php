<?php

declare(strict_types=1);

namespace App\Application\Member\Query;

use App\Application\Member\DTO\MemberProfileDTO;
use App\Domain\Member\Repository\MemberProfileRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Repository\UserRepositoryInterface;

final readonly class GetMemberProfileHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private MemberProfileRepositoryInterface $profileRepository
    ) {}

    public function __invoke(GetMemberProfileQuery $query): MemberProfileDTO
    {
        $userId = Uuid::fromString($query->userId);
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new EntityNotFoundException('User not found');
        }

        $profile = $this->profileRepository->findByUserId($userId);

        if ($profile === null) {
            throw new EntityNotFoundException('Member profile not found');
        }

        return new MemberProfileDTO(
            userId: (string)$user->getId(),
            email: (string)$user->getEmail(),
            name: $profile->getName(),
            surname: $profile->getSurname(),
            phoneNumber: $profile->getPhoneNumber(),
            department: $profile->getDepartment(),
            birthDate: $profile->getBirthDate()?->format('Y-m-d'),
            createdAt: $user->getCreatedAt()->format(\DateTimeInterface::ATOM)
        );
    }
}
