<?php

declare(strict_types=1);

namespace App\Application\Member\Query;

use App\Application\Member\DTO\MemberAccessLogDTO;
use App\Domain\Member\Repository\MemberAccessLogRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\Exception\ValidationException;

final readonly class GetMemberAccessLogByIdHandler
{
    public function __construct(
        private MemberAccessLogRepositoryInterface $accessLogRepository
    ) {}

    public function __invoke(GetMemberAccessLogByIdQuery $query): MemberAccessLogDTO
    {
        $accessLog = $this->accessLogRepository->findById($query->accessLogId);

        if (!$accessLog) {
            throw new EntityNotFoundException('Access log not found');
        }

        // Authorization check
        if ((string)$accessLog->getUserId() !== $query->userId) {
            throw new ValidationException('Unauthorized to view this access log');
        }

        return new MemberAccessLogDTO(
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
