<?php

declare(strict_types=1);

namespace App\Application\Admin\Query;

use App\Application\Admin\DTO\AdminAccessLogDTO;
use App\Domain\Admin\Repository\AdminAccessLogRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;

final readonly class GetAdminAccessLogByIdHandler
{
    public function __construct(
        private AdminAccessLogRepositoryInterface $accessLogRepository
    ) {}

    public function __invoke(GetAdminAccessLogByIdQuery $query): AdminAccessLogDTO
    {
        $accessLog = $this->accessLogRepository->findById($query->accessLogId);

        if ($accessLog === null) {
            throw new EntityNotFoundException('Access log not found');
        }

        // Authorization check
        if ((string)$accessLog->getUserId() !== $query->userId) {
            throw new ValidationException('Unauthorized to view this access log');
        }

        return new AdminAccessLogDTO(
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
        );
    }
}
