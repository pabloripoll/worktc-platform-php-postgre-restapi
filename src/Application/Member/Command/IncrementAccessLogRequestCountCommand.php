<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

final readonly class IncrementAccessLogRequestCountCommand
{
    public function __construct(public string $token) {}
}
