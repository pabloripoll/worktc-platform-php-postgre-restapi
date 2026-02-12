<?php

declare(strict_types=1);

namespace App\Presentation\Request\Admin;

use App\Presentation\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePasswordRequest extends BaseRequest
{
    #[Assert\NotBlank(message: 'Current password is required')]
    public string $currentPassword;

    #[Assert\NotBlank(message: 'New password is required')]
    #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters')]
    public string $newPassword;
}
