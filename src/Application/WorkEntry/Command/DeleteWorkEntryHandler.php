<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Command;

use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\WorkEntry\Repository\WorkEntryRepositoryInterface;

final readonly class DeleteWorkEntryHandler
{
    public function __construct(
        private WorkEntryRepositoryInterface $workEntryRepository
    ) {}

    public function __invoke(DeleteWorkEntryCommand $command): void
    {
        $workEntryId = Uuid::fromString($command->workEntryId);
        $workEntry = $this->workEntryRepository->findById($workEntryId);

        if ($workEntry === null) {
            throw new EntityNotFoundException('Work entry not found');
        }

        // Authorization check
        if ((string)$workEntry->getUserId() !== $command->userId) {
            throw new ValidationException('Unauthorized to delete this work entry');
        }

        $deletedByUserId = $command->deletedByUserId
            ? Uuid::fromString($command->deletedByUserId)
            : null;

        $workEntry->softDelete($deletedByUserId);
        $this->workEntryRepository->save($workEntry);
    }
}
