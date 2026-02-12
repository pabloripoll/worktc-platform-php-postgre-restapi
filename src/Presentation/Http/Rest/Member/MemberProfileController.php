<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Member;

use App\Application\Member\Command\UpdateMemberProfileCommand;
use App\Application\Member\Command\UpdateMemberProfileHandler;
use App\Application\Member\Query\GetMemberProfileHandler;
use App\Application\Member\Query\GetMemberProfileQuery;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\User\Entity\User;
use App\Presentation\Request\Member\UpdateMemberProfileRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/profile')]
#[IsGranted('ROLE_MEMBER')]
class MemberProfileController extends AbstractController
{
    #[Route('', name: 'member_profile_get', methods: ['GET'])]
    public function getProfile(
        #[CurrentUser] User $user,
        GetMemberProfileHandler $handler
    ): JsonResponse {
        try {
            $query = new GetMemberProfileQuery((string)$user->getId());
            $profile = $handler($query);

            return $this->json($profile);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'member_profile_update', methods: ['PATCH'])]
    public function updateProfile(
        UpdateMemberProfileRequest $request,
        #[CurrentUser] User $user,
        UpdateMemberProfileHandler $handler
    ): JsonResponse {
        $errors = $request->validate();
        if (!empty($errors)) {
            return $this->json([
                'error' => 'Validation failed',
                'violations' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $command = new UpdateMemberProfileCommand(
                userId: (string)$user->getId(),
                name: $request->name,
                surname: $request->surname,
                phoneNumber: $request->phone_number,
                department: $request->department,
                birthDate: $request->birth_date,
                currentPassword: $request->current_password,
                newPassword: $request->new_password,
            );

            $handler($command);

            return $this->json([
                'message' => 'Profile updated successfully'
            ]);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
