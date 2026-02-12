<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

class EntityNotFoundException extends DomainException
{
    public function __construct(string $message = 'Entity not found', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function forEntity(string $entityName, string $identifier): self
    {
        return new self("{$entityName} with identifier '{$identifier}' not found");
    }
}
