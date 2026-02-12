<?php

declare(strict_types=1);

namespace App\Application\User\Query;

use App\Application\User\DTO\UserDTO;
use App\Domain\Admin\Repository\AdminProfileRepositoryInterface;
use App\Domain\Member\Repository\MemberProfileRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Repository\UserRepositoryInterface;

final readonly class GetUserByIdHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AdminProfileRepositoryInterface $adminProfileRepository,
        private MemberProfileRepositoryInterface $memberProfileRepository
    ) {}

    public function __invoke(GetUserByIdQuery $query): UserDTO
    {
        $userId = Uuid::fromString($query->userId);
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new EntityNotFoundException("User not found");
        }

        // Load profile based on role
        $profile = null;
        if ($user->isAdmin()) {
            $profile = $this->adminProfileRepository->findByUserId($userId);
        } else {
            $profile = $this->memberProfileRepository->findByUserId($userId);
        }

        return new UserDTO(
            id: (string)$user->getId(),
            email: (string)$user->getEmail(),
            role: $user->getRole()->value,
            name: $profile?->getName(),
            surname: $profile?->getSurname(),
            phoneNumber: $profile?->getPhoneNumber(),
            department: $profile?->getDepartment()
        );
    }
}
