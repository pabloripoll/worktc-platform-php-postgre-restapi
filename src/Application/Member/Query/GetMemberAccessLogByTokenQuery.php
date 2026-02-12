<?php

declare(strict_types=1);

namespace App\Application\Member\Query;

final readonly class GetMemberAccessLogByTokenQuery
{
    public function __construct(
        public string $token,
        public string $userId // For authorization check
    ) {}
}
