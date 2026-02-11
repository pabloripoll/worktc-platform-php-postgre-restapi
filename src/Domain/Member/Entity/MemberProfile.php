<?php

declare(strict_types=1);

namespace App\Domain\Member\Entity;

use App\Domain\Shared\ValueObject\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'member_profiles')]
class MemberProfile
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $userId;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100)]
    private string $surname;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $birthDate = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $department = null;

    private function __construct(
        Uuid $userId,
        string $name,
        string $surname,
        ?\DateTimeImmutable $birthDate = null,
        ?string $phoneNumber = null,
        ?string $department = null
    ) {
        $this->userId = (string)$userId;
        $this->name = $name;
        $this->surname = $surname;
        $this->birthDate = $birthDate;
        $this->phoneNumber = $phoneNumber;
        $this->department = $department;
    }

    public static function create(
        Uuid $userId,
        string $name,
        string $surname,
        ?\DateTimeImmutable $birthDate = null,
        ?string $phoneNumber = null,
        ?string $department = null
    ): self {
        return new self($userId, $name, $surname, $birthDate, $phoneNumber, $department);
    }

    public function getUserId(): Uuid
    {
        return Uuid::fromString($this->userId);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getFullName(): string
    {
        return trim("{$this->name} {$this->surname}");
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function updateProfile(
        string $name,
        string $surname,
        ?\DateTimeImmutable $birthDate = null,
        ?string $phoneNumber = null,
        ?string $department = null
    ): void {
        $this->name = $name;
        $this->surname = $surname;
        $this->birthDate = $birthDate;
        $this->phoneNumber = $phoneNumber;
        $this->department = $department;
    }
}
