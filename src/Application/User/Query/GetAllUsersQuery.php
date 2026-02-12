<?php

declare(strict_types=1);

namespace App\Application\User\Query;

use App\Application\Shared\DTO\PaginationDTO;

final readonly class GetAllUsersQuery
{
    public function __construct(
        public string $role, // 'ROLE_ADMIN' or 'ROLE_MEMBER'
        public PaginationDTO $pagination
    ) {}
}
