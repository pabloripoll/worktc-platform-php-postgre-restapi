<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

final readonly class UpdateMemberProfileCommand
{
    public function __construct(
        public string $userId,
        public ?string $name = null,
        public ?string $surname = null,
        public ?string $phoneNumber = null,
        public ?string $department = null,
        public ?string $birthDate = null,
        public ?string $currentPassword = null,
        public ?string $newPassword = null,
    ) {}
}
