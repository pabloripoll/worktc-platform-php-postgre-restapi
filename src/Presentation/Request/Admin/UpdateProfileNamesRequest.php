<?php

declare(strict_types=1);

namespace App\Presentation\Request\Admin;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateProfileNamesRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name is required')]
        #[Assert\Length(min: 2, max: 64)]
        public string $name
    ) {}
}
