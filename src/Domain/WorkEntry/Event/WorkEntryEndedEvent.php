<?php

declare(strict_types=1);

namespace App\Domain\WorkEntry\Event;

use App\Domain\Shared\ValueObject\Uuid;

final readonly class WorkEntryEndedEvent
{
    public function __construct(
        public Uuid $workEntryId,
        public Uuid $userId,
        public \DateTimeImmutable $endDate,
        public \DateTimeImmutable $occurredOn
    ) {}

    public static function create(Uuid $workEntryId, Uuid $userId, \DateTimeImmutable $endDate): self
    {
        return new self($workEntryId, $userId, $endDate, new \DateTimeImmutable());
    }
}
