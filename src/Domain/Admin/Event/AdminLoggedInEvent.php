<?php

declare(strict_types=1);

namespace App\Domain\Admin\Event;

use App\Domain\Shared\ValueObject\Uuid;

final readonly class AdminLoggedInEvent
{
    public function __construct(
        public Uuid $adminId,
        public string $ipAddress,
        public \DateTimeImmutable $occurredOn
    ) {}

    public static function create(Uuid $adminId, string $ipAddress): self
    {
        return new self($adminId, $ipAddress, new \DateTimeImmutable());
    }
}
