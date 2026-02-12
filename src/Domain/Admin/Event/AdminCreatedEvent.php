<?php

declare(strict_types=1);

namespace App\Domain\Admin\Event;

use App\Domain\Shared\ValueObject\Uuid;

final readonly class AdminCreatedEvent
{
    public function __construct(
        public Uuid $adminId,
        public string $email,
        public \DateTimeImmutable $occurredOn
    ) {}

    public static function create(Uuid $adminId, string $email): self
    {
        return new self($adminId, $email, new \DateTimeImmutable());
    }
}
