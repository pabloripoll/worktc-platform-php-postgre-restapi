<?php

declare(strict_types=1);

namespace App\Application\User\Command;

final readonly class LoginCommand
{
    public function __construct(
        public string $email,
        public string $password,
        public string $role, // 'ROLE_ADMIN' or 'ROLE_MEMBER'
        public ?string $ipAddress = null,
        public ?string $userAgent = null
    ) {}
}
