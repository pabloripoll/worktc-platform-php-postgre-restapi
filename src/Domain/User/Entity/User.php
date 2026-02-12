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
#[ORM\Index(columns: ['email'], name: 'idx_user_email')]
#[ORM\Index(columns: ['role'], name: 'idx_user_role')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $role;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(type: 'string', length: 36)]
    private string $createdByUserId;

    private function __construct(
        Uuid $id,
        UserRole $role,
        Email $email,
        string $hashedPassword,
        Uuid $createdByUserId
    ) {
        /** @disregard P1006 Expected type 'null|int'. Found 'string' */
        $this->id = (string)$id;
        $this->role = $role->value;
        $this->email = (string)$email;
        $this->password = $hashedPassword;
        $this->createdByUserId = (string)$createdByUserId;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public static function createAdmin(
        Uuid $id,
        Email $email,
        string $hashedPassword,
        Uuid $createdByUserId
    ): self {
        return new self($id, UserRole::ADMIN, $email, $hashedPassword, $createdByUserId);
    }

    public static function createMember(
        Uuid $id,
        Email $email,
        string $hashedPassword,
        Uuid $createdByUserId
    ): self {
        return new self($id, UserRole::MEMBER, $email, $hashedPassword, $createdByUserId);
    }

    // Getters
    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getRole(): UserRole
    {
        return UserRole::from($this->role);
    }

    public function getEmail(): Email
    {
        return Email::fromString($this->email);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function getCreatedByUserId(): Uuid
    {
        return Uuid::fromString($this->createdByUserId);
    }

    // Business methods
    public function changePassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function softDelete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN->value;
    }

    public function isMember(): bool
    {
        return $this->role === UserRole::MEMBER->value;
    }

    // UserInterface implementation
    public function getRoles(): array
    {
        return [$this->role];
    }

    public function eraseCredentials(): void
    {
        // Intentionally empty - no sensitive temporary credentials to erase
        // Actual credentials are stored in $password property
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
