<?php

declare(strict_types=1);

namespace App\Presentation\Request\Admin;

use App\Presentation\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateProfileSurnamesRequest extends BaseRequest
{
    #[Assert\NotBlank(message: 'Surname is required')]
    #[Assert\Length(min: 2, max: 64)]
    public string $surname;
}
