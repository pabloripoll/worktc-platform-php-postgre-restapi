<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Query;

use App\Application\WorkEntry\DTO\WorkEntryDTO;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\WorkEntry\Repository\WorkEntryRepositoryInterface;

final readonly class GetWorkEntriesByUserHandler
{
    public function __construct(
        private WorkEntryRepositoryInterface $workEntryRepository
    ) {}

    /**
     * @return WorkEntryDTO[]
     */
    public function __invoke(GetWorkEntriesByUserQuery $query): array
    {
        $userId = Uuid::fromString($query->userId);
        $workEntries = $this->workEntryRepository->findByUserId($userId);

        return array_map(
            fn($workEntry) => new WorkEntryDTO(
                id: (string)$workEntry->getId(),
                userId: (string)$workEntry->getUserId(),
                startDate: $workEntry->getStartDate()->format(\DateTimeInterface::ATOM),
                endDate: $workEntry->getEndDate()?->format(\DateTimeInterface::ATOM),
                createdAt: $workEntry->getCreatedAt()->format(\DateTimeInterface::ATOM),
                updatedAt: $workEntry->getUpdatedAt()->format(\DateTimeInterface::ATOM),
                deletedAt: $workEntry->getDeletedAt()?->format(\DateTimeInterface::ATOM),
                durationMinutes: $workEntry->getDurationInMinutes()
            ),
            $workEntries
        );
    }
}
