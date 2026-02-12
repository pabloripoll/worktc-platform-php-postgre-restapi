<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

final readonly class UpdateMemberNamesCommand
{
    public function __construct(
        public string $userId,
        public string $name
    ) {}
}
