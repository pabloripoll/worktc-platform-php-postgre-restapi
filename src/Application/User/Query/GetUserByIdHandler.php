<?php

declare(strict_types=1);

namespace App\Application\User\Query;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class GetUserByIdHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EntityManagerInterface $em,
    ) {}

    public function __invoke(GetUserByIdQuery $query): array
    {
        $userId = Uuid::fromString($query->userId);

        // Clear any cached entities to ensure fresh data
        $this->em->clear();

        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new DomainException('User not found');
        }

        return [
            'id' => (string)$user->getId(),
            'email' => (string)$user->getEmail(),
            'role' => $user->getRole()->value,
            'name' => $user->getName(),
            'surname' => $user->getSurname(),
            'phone_number' => $user->getPhoneNumber(),
            'department' => $user->getDepartment(),
            'birth_date' => $user->getBirthDate()?->format('Y-m-d'),
            'created_at' => $user->getCreatedAt()->format('c'),
        ];
    }
}
