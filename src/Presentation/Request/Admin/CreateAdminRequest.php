<?php

declare(strict_types=1);

namespace App\Presentation\Request\Admin;

use App\Presentation\Request\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateAdminRequest extends BaseRequest
{
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Invalid email format')]
    public string $email;

    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters')]
    public string $password;

    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Length(min: 2, max: 64)]
    public string $name;

    #[Assert\NotBlank(message: 'Surname is required')]
    #[Assert\Length(min: 2, max: 64)]
    public string $surname;

    #[Assert\Length(max: 32)]
    public ?string $phoneNumber = null;

    #[Assert\Length(max: 64)]
    public ?string $department = null;

    public ?string $birthDate = null;
}
