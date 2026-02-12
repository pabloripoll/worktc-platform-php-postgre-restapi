<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Query;

final readonly class GetWorkEntriesByUserQuery
{
    public function __construct(public string $userId) {}
}
