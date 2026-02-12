<?php

declare(strict_types=1);

namespace App\Presentation\Request\Admin;

use App\Presentation\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateProfileNamesRequest extends BaseRequest
{
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Length(min: 2, max: 64)]
    public string $name;
}
