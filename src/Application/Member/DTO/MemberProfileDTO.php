<?php

declare(strict_types=1);

namespace App\Application\Member\DTO;

final readonly class MemberProfileDTO
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
