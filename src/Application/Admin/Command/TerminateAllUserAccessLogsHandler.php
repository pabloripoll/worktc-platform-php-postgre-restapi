<?php

declare(strict_types=1);

namespace App\Application\Admin\Command;

use App\Domain\Admin\Repository\AdminAccessLogRepositoryInterface;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;

final readonly class TerminateAllUserAccessLogsHandler
{
    public function __construct(
        private AdminAccessLogRepositoryInterface $accessLogRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(TerminateAllUserAccessLogsCommand $command): int
    {
        $userId = Uuid::fromString($command->userId);

        // Verify user exists
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new EntityNotFoundException('User not found');
        }

        // Get all active access logs
        $accessLogs = $this->accessLogRepository->findActiveByUserId($userId);

        foreach ($accessLogs as $accessLog) {
            $accessLog->terminate();
            $this->accessLogRepository->save($accessLog);
        }

        return count($accessLogs);
    }
}
