<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case MEMBER = 'ROLE_MEMBER';

    public function toString(): string
    {
        return $this->value;
    }
}
