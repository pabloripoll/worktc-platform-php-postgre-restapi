<?php

declare(strict_types=1);

namespace App\Application\Admin\Command;

use App\Domain\Admin\Repository\AdminAccessLogRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;

final readonly class IncrementAccessLogRequestCountHandler
{
    public function __construct(
        private AdminAccessLogRepositoryInterface $accessLogRepository
    ) {}

    public function __invoke(IncrementAccessLogRequestCountCommand $command): void
    {
        $accessLog = $this->accessLogRepository->findByToken($command->token);

        if ($accessLog === null) {
            throw new EntityNotFoundException('Access log not found');
        }

        $accessLog->incrementRequestCount();
        $this->accessLogRepository->save($accessLog);
    }
}
