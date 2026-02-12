<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Admin;

use App\Application\Admin\Command\UpdateAdminProfileCommand;
use App\Application\Admin\Command\UpdateAdminProfileHandler;
use App\Application\Admin\Query\GetAdminProfileHandler;
use App\Application\Admin\Query\GetAdminProfileQuery;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\User\Entity\User;
use App\Presentation\Request\Admin\UpdateAdminProfileRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin/profile')]
#[IsGranted('ROLE_ADMIN')]
class AdminProfileController extends AbstractController
{
    #[Route('', name: 'admin_profile_get', methods: ['GET'])]
    public function getProfile(
        #[CurrentUser] User $user,
        GetAdminProfileHandler $handler
    ): JsonResponse {
        try {
            $query = new GetAdminProfileQuery((string)$user->getId());
            $profile = $handler($query);

            return $this->json($profile);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'admin_profile_update', methods: ['PATCH'])]
    public function updateProfile(
        UpdateAdminProfileRequest $request,
        #[CurrentUser] User $user,
        UpdateAdminProfileHandler $handler
    ): JsonResponse {
        $errors = $request->validate();
        if (!empty($errors)) {
            return $this->json([
                'error' => 'Validation failed',
                'violations' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $command = new UpdateAdminProfileCommand(
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
