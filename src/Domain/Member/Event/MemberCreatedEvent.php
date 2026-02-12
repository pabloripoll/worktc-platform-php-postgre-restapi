<?php

declare(strict_types=1);

namespace App\Domain\Member\Event;

use App\Domain\Shared\ValueObject\Uuid;

final readonly class MemberCreatedEvent
{
    public function __construct(
        public Uuid $memberId,
        public string $email,
        public \DateTimeImmutable $occurredOn
    ) {}

    public static function create(Uuid $memberId, string $email): self
    {
        return new self($memberId, $email, new \DateTimeImmutable());
    }
}
