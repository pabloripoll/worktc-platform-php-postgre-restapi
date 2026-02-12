<?php

declare(strict_types=1);

namespace App\Presentation\Request\Admin;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdatePasswordRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Current password is required')]
        public string $current_password,

        #[Assert\NotBlank(message: 'New password is required')]
        #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters')]
        public string $new_password
    ) {}
}
