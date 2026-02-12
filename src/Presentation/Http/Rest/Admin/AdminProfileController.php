<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Admin;

use App\Application\Admin\Command\UpdateAdminNamesCommand;
use App\Application\Admin\Command\UpdateAdminNamesHandler;
use App\Application\Admin\Command\UpdateAdminPasswordCommand;
use App\Application\Admin\Command\UpdateAdminPasswordHandler;
use App\Application\Admin\Command\UpdateAdminSurnamesCommand;
use App\Application\Admin\Command\UpdateAdminSurnamesHandler;
use App\Application\Admin\Query\GetAdminProfileHandler;
use App\Application\Admin\Query\GetAdminProfileQuery;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\User\Entity\User;
use App\Presentation\Request\Admin\UpdatePasswordRequest;
use App\Presentation\Request\Admin\UpdateProfileNamesRequest;
use App\Presentation\Request\Admin\UpdateProfileSurnamesRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/names', name: 'admin_profile_update_names', methods: ['PATCH'])]
    public function updateNames(
        UpdateProfileNamesRequest $request,
        #[CurrentUser] User $user,
        UpdateAdminNamesHandler $handler
    ): JsonResponse {
        try {
            $command = new UpdateAdminNamesCommand(
                userId: (string)$user->getId(),
                name: $request->name
            );

            $handler($command);

            return $this->json(['message' => 'Names updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/surnames', name: 'admin_profile_update_surnames', methods: ['PATCH'])]
    public function updateSurnames(
        UpdateProfileSurnamesRequest $request,
        #[CurrentUser] User $user,
        UpdateAdminSurnamesHandler $handler
    ): JsonResponse {
        try {
            $command = new UpdateAdminSurnamesCommand(
                userId: (string)$user->getId(),
                surname: $request->surname
            );

            $handler($command);

            return $this->json(['message' => 'Surnames updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/password', name: 'admin_profile_update_password', methods: ['PATCH'])]
    public function updatePassword(
        UpdatePasswordRequest $request,
        #[CurrentUser] User $user,
        UpdateAdminPasswordHandler $handler
    ): JsonResponse {
        try {
            $command = new UpdateAdminPasswordCommand(
                userId: (string)$user->getId(),
                currentPassword: $request->currentPassword,
                newPassword: $request->newPassword
            );

            $handler($command);

            return $this->json(['message' => 'Password updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
