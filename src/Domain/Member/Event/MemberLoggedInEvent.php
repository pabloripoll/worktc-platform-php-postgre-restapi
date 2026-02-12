<?php

declare(strict_types=1);

namespace App\Domain\Member\Event;

use App\Domain\Shared\ValueObject\Uuid;

final readonly class MemberLoggedInEvent
{
    public function __construct(
        public Uuid $memberId,
        public string $ipAddress,
        public \DateTimeImmutable $occurredOn
    ) {}

    public static function create(Uuid $memberId, string $ipAddress): self
    {
        return new self($memberId, $ipAddress, new \DateTimeImmutable());
    }
}
