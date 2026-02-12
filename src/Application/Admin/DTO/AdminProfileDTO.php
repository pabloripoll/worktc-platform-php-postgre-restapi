<?php

declare(strict_types=1);

namespace App\Application\Admin\DTO;

final readonly class AdminProfileDTO
{
    public function __construct(
        public string $userId,
        public string $email,
        public string $name,
        public string $surname,
        public ?string $phoneNumber,
        public ?string $department,
        public ?string $birthDate,
        public string $createdAt
    ) {}
}
