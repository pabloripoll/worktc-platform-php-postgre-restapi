<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

use App\Domain\Member\Repository\MemberAccessLogRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;

final readonly class TerminateAccessLogHandler
{
    public function __construct(
        private MemberAccessLogRepositoryInterface $accessLogRepository
    ) {}

    public function __invoke(TerminateAccessLogCommand $command): void
    {
        $accessLog = $this->accessLogRepository->findById($command->accessLogId);

        if (!$accessLog) {
            throw new EntityNotFoundException('Access log not found');
        }

        // Authorization check
        if ((string)$accessLog->getUserId() !== $command->userId) {
            throw new ValidationException('Unauthorized to terminate this access log');
        }

        if ($accessLog->isTerminated()) {
            throw new ValidationException('Access log already terminated');
        }

        $accessLog->terminate();
        $this->accessLogRepository->save($accessLog);
    }
}
