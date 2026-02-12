<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Shared\ValueObject\Email;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\ValueObject\UserRole;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'string', enumType: UserRole::class)]
    private UserRole $role;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $department = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $birthDate = null;

    #[ORM\Column(type: 'uuid')]
    private string $createdBy;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    private function __construct(
        Uuid $id,
        Email $email,
        string $password,
        UserRole $role,
        Uuid $createdBy
    ) {
        $this->id = (string)$id;
        $this->email = (string)$email;
        $this->password = $password;
        $this->role = $role;
        $this->createdBy = (string)$createdBy;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function createAdmin(
        Uuid $id,
        Email $email,
        string $password,
        Uuid $createdBy
    ): self {
        return new self($id, $email, $password, UserRole::ADMIN, $createdBy);
    }

    public static function createMember(
        Uuid $id,
        Email $email,
        string $password,
        Uuid $createdBy
    ): self {
        return new self($id, $email, $password, UserRole::MEMBER, $createdBy);
    }

    // Getters
    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getEmail(): Email
    {
        return Email::fromString($this->email);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function getCreatedBy(): Uuid
    {
        return Uuid::fromString($this->createdBy);
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    // Update methods
    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->markAsUpdated();
    }

    public function updateSurname(string $surname): void
    {
        $this->surname = $surname;
        $this->markAsUpdated();
    }

    public function updatePhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
        $this->markAsUpdated();
    }

    public function updateDepartment(?string $department): void
    {
        $this->department = $department;
        $this->markAsUpdated();
    }

    public function updateBirthDate(?string $birthDate): void
    {
        if ($birthDate === null) {
            $this->birthDate = null;
        } else {
            $this->birthDate = new \DateTimeImmutable($birthDate);
        }
        $this->markAsUpdated();
    }

    public function updatePassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
        $this->markAsUpdated();
    }

    public function updateEmail(Email $email): void
    {
        $this->email = (string)$email;
        $this->markAsUpdated();
    }

    public function softDelete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
        $this->markAsUpdated();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
        $this->markAsUpdated();
    }

    private function markAsUpdated(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Role checks
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isMember(): bool
    {
        return $this->role === UserRole::MEMBER;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    // Symfony UserInterface implementation
    public function getRoles(): array
    {
        return [$this->role->value];
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Intentionally empty - no sensitive temporary credentials to erase
    }
}
