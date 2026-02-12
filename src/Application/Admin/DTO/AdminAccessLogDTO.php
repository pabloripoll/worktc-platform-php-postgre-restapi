<?php

declare(strict_types=1);

namespace App\Application\Admin\DTO;

final readonly class AdminAccessLogDTO
{
    public function __construct(
        public ?int $id,
        public string $userId,
        public string $token,
        public bool $isTerminated,
        public bool $isExpired,
        public string $expiresAt,
        public int $refreshCount,
        public int $requestsCount,
        public ?string $ipAddress,
        public ?string $userAgent,
        public string $createdAt,
        public string $updatedAt,
        public ?array $payload
    ) {}
}
