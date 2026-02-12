<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Command;

use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\WorkEntry\Repository\WorkEntryRepositoryInterface;

final readonly class UpdateWorkEntryHandler
{
    public function __construct(
        private WorkEntryRepositoryInterface $workEntryRepository
    ) {}

    public function __invoke(UpdateWorkEntryCommand $command): void
    {
        $workEntryId = Uuid::fromString($command->workEntryId);
        $workEntry = $this->workEntryRepository->findById($workEntryId);

        if (!$workEntry) {
            throw new EntityNotFoundException('Work entry not found');
        }

        // Authorization check
        if ((string)$workEntry->getUserId() !== $command->userId) {
            throw new ValidationException('Unauthorized to update this work entry');
        }

        if ($workEntry->isDeleted()) {
            throw new ValidationException('Cannot update deleted work entry');
        }

        // Update end date if provided
        if ($command->endDate && !$workEntry->getEndDate()) {
            $endDate = new \DateTimeImmutable($command->endDate);
            $updatedByUserId = $command->updatedByUserId
                ? Uuid::fromString($command->updatedByUserId)
                : null;

            $workEntry->end($endDate, $updatedByUserId);
        }

        $this->workEntryRepository->save($workEntry);
    }
}
