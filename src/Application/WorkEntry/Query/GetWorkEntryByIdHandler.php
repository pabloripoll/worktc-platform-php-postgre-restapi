<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Query;

use App\Application\WorkEntry\DTO\WorkEntryDTO;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\WorkEntry\Repository\WorkEntryRepositoryInterface;

final readonly class GetWorkEntryByIdHandler
{
    public function __construct(
        private WorkEntryRepositoryInterface $workEntryRepository
    ) {}

    public function __invoke(GetWorkEntryByIdQuery $query): WorkEntryDTO
    {
        $workEntryId = Uuid::fromString($query->workEntryId);
        $workEntry = $this->workEntryRepository->findById($workEntryId);

        if (!$workEntry) {
            throw new EntityNotFoundException('Work entry not found');
        }

        // Authorization check
        if ((string)$workEntry->getUserId() !== $query->userId) {
            throw new ValidationException('Unauthorized to view this work entry');
        }

        return new WorkEntryDTO(
            id: (string)$workEntry->getId(),
            userId: (string)$workEntry->getUserId(),
            startDate: $workEntry->getStartDate()->format(\DateTimeInterface::ATOM),
            endDate: $workEntry->getEndDate()?->format(\DateTimeInterface::ATOM),
            createdAt: $workEntry->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $workEntry->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            deletedAt: $workEntry->getDeletedAt()?->format(\DateTimeInterface::ATOM),
            durationMinutes: $workEntry->getDurationInMinutes()
        );
    }
}
