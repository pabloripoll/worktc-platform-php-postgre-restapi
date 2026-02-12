<?php

declare(strict_types=1);

namespace App\Application\Shared\DTO;

final readonly class PaginatedResultDTO
{
    /**
     * @param array<int, mixed> $data
     */
    public function __construct(
        public array $data,
        public PaginationDTO $pagination
    ) {}

    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'pagination' => [
                'page' => $this->pagination->page,
                'limit' => $this->pagination->limit,
                'total' => $this->pagination->total,
                'total_pages' => $this->pagination->totalPages,
            ]
        ];
    }
}
