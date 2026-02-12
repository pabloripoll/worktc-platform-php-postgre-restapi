<?php

declare(strict_types=1);

namespace App\Application\Admin\Query;

use App\Application\Admin\DTO\AdminProfileDTO;
use App\Domain\Admin\Repository\AdminProfileRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Repository\UserRepositoryInterface;

final readonly class GetAdminProfileHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AdminProfileRepositoryInterface $profileRepository
    ) {}

    public function __invoke(GetAdminProfileQuery $query): AdminProfileDTO
    {
        $userId = Uuid::fromString($query->userId);
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new EntityNotFoundException('User not found');
        }

        $profile = $this->profileRepository->findByUserId($userId);

        if ($profile === null) {
            throw new EntityNotFoundException('Admin profile not found');
        }

        return new AdminProfileDTO(
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
