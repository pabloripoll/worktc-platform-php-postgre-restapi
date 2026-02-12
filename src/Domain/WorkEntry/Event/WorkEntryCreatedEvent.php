<?php

declare(strict_types=1);

namespace App\Domain\WorkEntry\Event;

use App\Domain\Shared\ValueObject\Uuid;

final readonly class WorkEntryCreatedEvent
{
    public function __construct(
        public Uuid $workEntryId,
        public Uuid $userId,
        public \DateTimeImmutable $startDate,
        public \DateTimeImmutable $occurredOn
    ) {}

    public static function create(Uuid $workEntryId, Uuid $userId, \DateTimeImmutable $startDate): self
    {
        return new self($workEntryId, $userId, $startDate, new \DateTimeImmutable());
    }
}
