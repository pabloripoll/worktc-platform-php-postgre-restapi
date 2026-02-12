<?php

declare(strict_types=1);

namespace App\Presentation\Request\Admin;

use App\Presentation\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateAdminProfileRequest extends BaseRequest
{
    #[Assert\Length(min: 2, max: 64)]
    public ?string $name = null;

    #[Assert\Length(min: 2, max: 64)]
    public ?string $surname = null;

    #[Assert\Length(max: 20)]
    public ?string $phone_number = null;

    #[Assert\Length(max: 100)]
    public ?string $department = null;

    public ?string $birth_date = null;

    public ?string $current_password = null;

    #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters')]
    public ?string $new_password = null;
}
