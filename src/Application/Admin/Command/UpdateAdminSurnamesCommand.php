<?php

declare(strict_types=1);

namespace App\Application\Admin\Command;

final readonly class UpdateAdminSurnamesCommand
{
    public function __construct(
        public string $userId,
        public string $surname
    ) {}
}
