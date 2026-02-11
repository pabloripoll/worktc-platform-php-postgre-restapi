<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidUuidException;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

final readonly class Uuid
{
    private function __construct(public string $value)
    {
        if (!SymfonyUuid::isValid($value)) {
            throw new InvalidUuidException($value);
        }
    }

    public static function generate(): self
    {
        return new self(SymfonyUuid::v7()->toRfc4122());
    }

    public static function fromString(string $uuid): self
    {
        return new self($uuid);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
