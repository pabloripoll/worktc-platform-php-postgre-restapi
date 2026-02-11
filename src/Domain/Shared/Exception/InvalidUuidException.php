<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class InvalidUuidException extends \InvalidArgumentException
{
    public function __construct(string $value)
    {
        parent::__construct(sprintf('The value "%s" is not a valid UUID.', $value));
    }
}
