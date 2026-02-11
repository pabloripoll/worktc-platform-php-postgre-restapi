<?php

declare(strict_types=1);

namespace App\Domain\WorkEntry\Entity;

use App\Domain\Shared\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'work_entries')]
#[ORM\Index(columns: ['user_id'], name: 'idx_work_entry_user')]
#[ORM\Index(columns: ['start_date'], name: 'idx_work_entry_start')]
class WorkEntry
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $userId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $createdByUserId = null;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $updatedByUserId = null;

    private function __construct(
        Uuid $id,
        Uuid $userId,
        \DateTimeImmutable $startDate,
        ?Uuid $createdByUserId = null
    ) {
        $this->id = (string)$id;
        $this->userId = (string)$userId;
        $this->startDate = $startDate;
        $this->createdByUserId = $createdByUserId ? (string)$createdByUserId : null;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function start(
        Uuid $id,
        Uuid $userId,
        \DateTimeImmutable $startDate,
        ?Uuid $createdByUserId = null
    ): self {
        return new self($id, $userId, $startDate, $createdByUserId);
    }

    // Getters
    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getUserId(): Uuid
    {
        return Uuid::fromString($this->userId);
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function getCreatedByUserId(): ?Uuid
    {
        return $this->createdByUserId ? Uuid::fromString($this->createdByUserId) : null;
    }

    public function getUpdatedByUserId(): ?Uuid
    {
        return $this->updatedByUserId ? Uuid::fromString($this->updatedByUserId) : null;
    }

    // Business methods
    public function end(\DateTimeImmutable $endDate, ?Uuid $updatedByUserId = null): void
    {
        if ($endDate < $this->startDate) {
            throw new \DomainException('End date cannot be before start date.');
        }

        if ($this->endDate !== null) {
            throw new \DomainException('Work entry already ended.');
        }

        $this->endDate = $endDate;
        $this->updatedByUserId = $updatedByUserId ? (string)$updatedByUserId : null;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isActive(): bool
    {
        return $this->endDate === null && $this->deletedAt === null;
    }

    public function getDuration(): ?\DateInterval
    {
        if ($this->endDate === null) {
            return null;
        }

        return $this->startDate->diff($this->endDate);
    }

    public function getDurationInMinutes(): ?int
    {
        $duration = $this->getDuration();
        if ($duration === null) {
            return null;
        }

        return ($duration->days * 24 * 60) + ($duration->h * 60) + $duration->i;
    }

    public function softDelete(?Uuid $deletedByUserId = null): void
    {
        $this->deletedAt = new \DateTimeImmutable();
        $this->updatedByUserId = $deletedByUserId ? (string)$deletedByUserId : null;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
