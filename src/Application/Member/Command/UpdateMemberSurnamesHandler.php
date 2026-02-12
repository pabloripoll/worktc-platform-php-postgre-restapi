<?php

declare(strict_types=1);

namespace App\Application\Member\Command;

use App\Domain\Member\Repository\MemberProfileRepositoryInterface;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\ValueObject\Uuid;

final readonly class UpdateMemberSurnamesHandler
{
    public function __construct(
        private MemberProfileRepositoryInterface $profileRepository
    ) {}

    public function __invoke(UpdateMemberSurnamesCommand $command): void
    {
        $userId = Uuid::fromString($command->userId);
        $profile = $this->profileRepository->findByUserId($userId);

        if (!$profile) {
            throw new EntityNotFoundException('Member profile not found');
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
