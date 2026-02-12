<?php

declare(strict_types=1);

namespace App\Application\Shared\DTO;

final readonly class PaginationDTO
{
    public function __construct(
        public int $page = 1,
        public int $limit = 20,
        public int $total = 0,
        public int $totalPages = 0
    ) {}

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->limit;
    }

    public static function fromRequest(array $queryParams): self
    {
        $page = max(1, (int)($queryParams['page'] ?? 1));
        $limit = min(100, max(1, (int)($queryParams['limit'] ?? 20)));

        return new self(page: $page, limit: $limit);
    }

    public function withTotal(int $total): self
    {
        $totalPages = (int)ceil($total / $this->limit);
        return new self($this->page, $this->limit, $total, $totalPages);
    }
}
