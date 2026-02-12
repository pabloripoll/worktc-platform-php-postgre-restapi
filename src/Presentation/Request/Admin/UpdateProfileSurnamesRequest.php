<?php

declare(strict_types=1);

namespace App\Presentation\Request\Admin;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateProfileSurnamesRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Surname is required')]
        #[Assert\Length(min: 2, max: 64)]
        public string $surname
    ) {}
}
