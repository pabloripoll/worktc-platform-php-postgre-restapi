<?php

declare(strict_types=1);

namespace App\Application\Member\Query;

/**
 * Admin use case: get all member access logs
 */
final readonly class GetAllMemberAccessLogsQuery
{
    public function __construct(
        public bool $activeOnly = false,
        public ?string $userId = null // Optional filter by user
    ) {}
}
