<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\DomainException;
use Symfony\Component\Uid\Uuid as SymfonyUuid;

final readonly class Uuid
{
    private function __construct(
        private string $value
    ) {
        if (!SymfonyUuid::isValid($value)) {
            throw new DomainException('Invalid UUID format');
        }
    }

    /**
     * Create a new random UUID v7 (time-based, sortable)
     *
     * @return self
     */
    public static function random(): self
    {
        return new self(SymfonyUuid::v7()->toRfc4122());
    }

    /**
     * Create a new random UUID v7 (time-based, sortable)
     * Alias for random() for better semantic clarity
     *
     * @return self
     */
    public static function create(): self
    {
        return self::random();
    }

    /**
     * Generate a new random UUID v7 (time-based, sortable)
     * Alias for random() for backward compatibility
     *
     * @return self
     */
    public static function generate(): self
    {
        return self::random();
    }

    /**
     * Create UUID from string
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Get string representation
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Get the UUID value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Compare two UUIDs for equality
     *
     * @param Uuid $other
     * @return bool
     */
    public function equals(Uuid $other): bool
    {
        return $this->value === $other->value;
    }
}
