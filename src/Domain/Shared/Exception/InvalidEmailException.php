<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

class InvalidEmailException extends ValidationException
{
    public function __construct(string $email)
    {
        parent::__construct("The value '{$email}' is not a valid email address");
    }
}
