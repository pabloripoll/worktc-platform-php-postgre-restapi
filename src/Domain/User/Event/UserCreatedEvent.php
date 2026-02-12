<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\Shared\ValueObject\Uuid;

final readonly class UserCreatedEvent
{
    public function __construct(
        public Uuid $userId,
        public string $email,
        public string $role,
        public \DateTimeImmutable $occurredOn
    ) {}

    public static function create(Uuid $userId, string $email, string $role): self
    {
        return new self($userId, $email, $role, new \DateTimeImmutable());
    }
}
