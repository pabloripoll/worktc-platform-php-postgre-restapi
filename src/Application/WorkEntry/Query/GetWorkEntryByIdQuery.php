<?php

declare(strict_types=1);

namespace App\Application\WorkEntry\Query;

final readonly class GetWorkEntryByIdQuery
{
    public function __construct(
        public string $workEntryId,
        public string $userId // For authorization
    ) {}
}
