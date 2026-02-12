<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

use App\Domain\Member\Repository\MemberProfileRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\ValueObject\Uuid;

final readonly class UpdateMemberNamesHandler
{
    public function __construct(
        private MemberProfileRepositoryInterface $profileRepository
    ) {}

    public function __invoke(UpdateMemberNamesCommand $command): void
    {
        $userId = Uuid::fromString($command->userId);
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new EntityNotFoundException('Member profile not found');
        }

        $profile->updateProfile(
            $command->name,
            $profile->getSurname(),
            $profile->getBirthDate(),
            $profile->getPhoneNumber(),
            $profile->getDepartment()
        );

        $this->profileRepository->save($profile);
    }
}
