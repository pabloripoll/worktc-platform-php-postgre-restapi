<?php

declare(strict_types=1);

namespace App\Application\Admin\Query;

use App\Application\Admin\DTO\AdminAccessLogDTO;
use App\Domain\Admin\Repository\AdminAccessLogRepositoryInterface;
use App\Domain\Shared\ValueObject\Uuid;

final readonly class GetAdminAccessLogsHandler
{
    public function __construct(
        private AdminAccessLogRepositoryInterface $accessLogRepository
    ) {}

    /**
     * @return AdminAccessLogDTO[]
     */
    public function __invoke(GetAdminAccessLogsQuery $query): array
    {
        $userId = Uuid::fromString($query->userId);

        $accessLogs = $query->activeOnly
            ? $this->accessLogRepository->findActiveByUserId($userId)
            : $this->accessLogRepository->findByUserId($userId);

        return array_map(
            fn($accessLog) => new AdminAccessLogDTO(
                id: $accessLog->getId(),
                userId: (string)$accessLog->getUserId(),
                token: $accessLog->getToken(),
                isTerminated: $accessLog->isTerminated(),
                isExpired: $accessLog->isExpired(),
                expiresAt: $accessLog->getExpiresAt()->format(\DateTimeInterface::ATOM),
                refreshCount: $accessLog->getRefreshCount(),
                requestsCount: $accessLog->getRequestsCount(),
                ipAddress: $accessLog->getIpAddress(),
                userAgent: $accessLog->getUserAgent(),
                createdAt: $accessLog->getCreatedAt()->format(\DateTimeInterface::ATOM),
                updatedAt: $accessLog->getUpdatedAt()->format(\DateTimeInterface::ATOM),
                payload: $accessLog->getPayload()
            ),
            $accessLogs
        );
    }
}
