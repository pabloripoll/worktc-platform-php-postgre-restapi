<?php

declare(strict_types=1);

namespace App\Application\Admin\Query;

final readonly class GetAdminProfileQuery
{
    public function __construct(public string $userId) {}
}
