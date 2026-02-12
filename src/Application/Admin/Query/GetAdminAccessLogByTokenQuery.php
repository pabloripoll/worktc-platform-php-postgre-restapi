<?php

declare(strict_types=1);

namespace App\Application\Admin\Query;

final readonly class GetAdminAccessLogByTokenQuery
{
    public function __construct(
        public string $token,
        public string $userId // For authorization check
    ) {}
}
