<?php

declare(strict_types=1);

namespace App\Application\Admin\Command;

use App\Domain\Admin\Repository\AdminProfileRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\ValueObject\Uuid;

final readonly class UpdateAdminSurnamesHandler
{
    public function __construct(
        private AdminProfileRepositoryInterface $profileRepository
    ) {}

    public function __invoke(UpdateAdminSurnamesCommand $command): void
    {
        $userId = Uuid::fromString($command->userId);
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new EntityNotFoundException('Admin profile not found');
        }

        $profile->updateProfile(
            $profile->getName(),
            $command->surname,
            $profile->getBirthDate(),
            $profile->getPhoneNumber(),
            $profile->getDepartment()
        );

        $this->profileRepository->save($profile);
    }
}
