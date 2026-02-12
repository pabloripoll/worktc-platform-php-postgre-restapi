<?php

declare(strict_types=1);

namespace App\Application\User\Command;

final readonly class RegisterMemberCommand
{
    public function __construct(
        public string $email,
        public string $password,
        public string $name,
        public string $surname,
        public ?string $phoneNumber = null,
        public ?string $department = null,
        public ?string $birthDate = null,
        public string $createdByUserId // Admin who creates the member
    ) {}
}
