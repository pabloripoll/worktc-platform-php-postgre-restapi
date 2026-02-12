<?php

declare(strict_types=1);

namespace App\Application\Admin\Query;

final readonly class GetAdminAccessLogByIdQuery
{
    public function __construct(
        public int $accessLogId,
        public string $userId // For authorization check
    ) {}
}
