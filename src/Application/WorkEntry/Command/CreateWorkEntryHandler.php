<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Command;

use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\WorkEntry\Entity\WorkEntry;
use App\Domain\WorkEntry\Repository\WorkEntryRepositoryInterface;

final readonly class CreateWorkEntryHandler
{
    public function __construct(
        private WorkEntryRepositoryInterface $workEntryRepository
    ) {}

    public function __invoke(CreateWorkEntryCommand $command): Uuid
    {
        $workEntryId = Uuid::generate();
        $userId = Uuid::fromString($command->userId);
        $startDate = new \DateTimeImmutable($command->startDate);
        $createdByUserId = $command->createdByUserId
            ? Uuid::fromString($command->createdByUserId)
            : null;

        $workEntry = WorkEntry::start(
            $workEntryId,
            $userId,
            $startDate,
            $createdByUserId
        );

        // If endDate provided, end it immediately
        if ($command->endDate) {
            $endDate = new \DateTimeImmutable($command->endDate);
            $workEntry->end($endDate, $createdByUserId);
        }

        $this->workEntryRepository->save($workEntry);

        return $workEntryId;
    }
}
