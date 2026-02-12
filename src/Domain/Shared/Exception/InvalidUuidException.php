<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

class InvalidUuidException extends ValidationException
{
    public function __construct(string $value)
    {
        parent::__construct("The value '{$value}' is not a valid UUID");
    }
}
