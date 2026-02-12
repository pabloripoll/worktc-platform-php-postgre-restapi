<?php

declare(strict_types=1);

namespace App\Application\Member\Query;

final readonly class GetMemberProfileQuery
{
    public function __construct(public string $userId) {}
}
