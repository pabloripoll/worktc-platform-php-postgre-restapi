<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Shared\ValueObject\Uuid;

final readonly class UserLoggedInEvent
{
    public function __construct(
        public Uuid $userId,
        public string $ipAddress,
        public \DateTimeImmutable $occurredOn
    ) {}

    public static function create(Uuid $userId, string $ipAddress): self
    {
        return new self($userId, $ipAddress, new \DateTimeImmutable());
    }
}
