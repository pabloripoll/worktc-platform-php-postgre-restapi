<?php

declare(strict_types=1);

namespace App\Application\User\Query;

use App\Application\Shared\DTO\PaginatedResultDTO;
use App\Application\User\DTO\UserDTO;
use App\Domain\Admin\Repository\AdminProfileRepositoryInterface;
use App\Domain\Member\Repository\MemberProfileRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserRole;

final readonly class GetAllUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AdminProfileRepositoryInterface $adminProfileRepository,
        private MemberProfileRepositoryInterface $memberProfileRepository
    ) {}

    public function __invoke(GetAllUsersQuery $query): PaginatedResultDTO
    {
        $role = $query->role;

        $result = $this->userRepository->findByRolePaginated(
            $role,
            $query->pagination->page,  // page number
            $query->pagination->limit  // items per page
        );

        $users = $result['data'];
        $total = $result['total'];

        // Map to DTOs
        $userDTOs = array_map(function($user) use ($role) {
            $profile = $role === UserRole::ADMIN
                ? $this->adminProfileRepository->findByUserId($user->getId())
                : $this->memberProfileRepository->findByUserId($user->getId());

            return new UserDTO(
                id: (string)$user->getId(),
                email: (string)$user->getEmail(),
                role: $user->getRole()->value,
                name: $profile?->getName(),
                surname: $profile?->getSurname(),
                phoneNumber: $profile?->getPhoneNumber(),
                department: $profile?->getDepartment()
            );
        }, $users);

        $pagination = $query->pagination->withTotal($total);

        return new PaginatedResultDTO($userDTOs, $pagination);
    }
}
