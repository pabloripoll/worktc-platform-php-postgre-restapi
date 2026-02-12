<?php

declare(strict_types=1);

namespace App\Application\Admin\Command;

final readonly class UpdateAdminNamesCommand
{
    public function __construct(
        public string $userId,
        public string $name
    ) {}
}
