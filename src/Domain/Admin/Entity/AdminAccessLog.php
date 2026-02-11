<?php

declare(strict_types=1);

namespace App\Domain\Admin\Entity;

use App\Domain\Shared\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'admin_access_logs')]
#[ORM\Index(columns: ['user_id'], name: 'idx_admin_access_log_user')]
#[ORM\Index(columns: ['token'], name: 'idx_admin_access_log_token')]
#[ORM\Index(columns: ['expires_at'], name: 'idx_admin_access_log_expires')]
#[ORM\Index(columns: ['created_at'], name: 'idx_admin_access_log_created')]
class AdminAccessLog
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 36)]
    private string $userId;

    #[ORM\Column(type: 'boolean')]
    private bool $isTerminated = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isExpired = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    #[ORM\Column(type: 'integer')]
    private int $refreshCount = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'integer')]
    private int $requestsCount = 0;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $payload = null;

    #[ORM\Column(type: 'text')]
    private string $token;

    private function __construct(
        Uuid $userId,
        string $token,
        \DateTimeImmutable $expiresAt,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?array $payload = null
    ) {
        $this->userId = (string)$userId;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->payload = $payload;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function create(
        Uuid $userId,
        string $token,
        \DateTimeImmutable $expiresAt,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?array $payload = null
    ): self {
        return new self($userId, $token, $expiresAt, $ipAddress, $userAgent, $payload);
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): Uuid
    {
        return Uuid::fromString($this->userId);
    }

    public function isTerminated(): bool
    {
        return $this->isTerminated;
    }

    public function isExpired(): bool
    {
        return $this->isExpired;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getRefreshCount(): int
    {
        return $this->refreshCount;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getRequestsCount(): int
    {
        return $this->requestsCount;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    // Business methods
    public function incrementRequestCount(): void
    {
        $this->requestsCount++;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function refresh(string $newToken, \DateTimeImmutable $newExpiresAt): void
    {
        $this->token = $newToken;
        $this->expiresAt = $newExpiresAt;
        $this->refreshCount++;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function terminate(): void
    {
        $this->isTerminated = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function markAsExpired(): void
    {
        $this->isExpired = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function checkAndMarkExpired(): bool
    {
        $now = new \DateTimeImmutable();
        if ($now > $this->expiresAt && !$this->isExpired) {
            $this->markAsExpired();
            return true;
        }
        return $this->isExpired;
    }
}
