<?php

declare(strict_types=1);

namespace App\Application\Member\Query;

use App\Application\Member\DTO\MemberAccessLogDTO;
use App\Domain\Member\Repository\MemberAccessLogRepositoryInterface;
use App\Domain\Shared\ValueObject\Uuid;

/**
 * Admin use case: get all member access logs across all members
 */
final readonly class GetAllMemberAccessLogsHandler
{
    public function __construct(
        private MemberAccessLogRepositoryInterface $accessLogRepository
    ) {}

    /**
     * @return MemberAccessLogDTO[]
     */
    public function __invoke(GetAllMemberAccessLogsQuery $query): array
    {
        if ($query->userId) {
            $userId = Uuid::fromString($query->userId);
            $accessLogs = $query->activeOnly
                ? $this->accessLogRepository->findActiveByUserId($userId)
                : $this->accessLogRepository->findByUserId($userId);
        } else {
            $accessLogs = $query->activeOnly
                ? $this->accessLogRepository->findAllActive()
                : $this->accessLogRepository->findAll();
        }

        return array_map(
            fn($accessLog) => new MemberAccessLogDTO(
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
