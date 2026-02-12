<?php

declare(strict_types=1);

namespace App\Application\User\DTO;

final readonly class UserDTO
{
    public function __construct(
        public string $id,
        public string $email,
        public string $role,
        public ?string $name = null,
        public ?string $surname = null,
        public ?string $phoneNumber = null,
        public ?string $department = null
    ) {}
}
