<?php

declare(strict_types=1);

namespace App\Application\Member\Query;

final readonly class GetMemberAccessLogByIdQuery
{
    public function __construct(
        public int $accessLogId,
        public string $userId // For authorization check
    ) {}
}
