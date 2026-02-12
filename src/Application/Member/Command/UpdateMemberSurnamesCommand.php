<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

final readonly class UpdateMemberSurnamesCommand
{
    public function __construct(
        public string $userId,
        public string $surname
    ) {}
}
