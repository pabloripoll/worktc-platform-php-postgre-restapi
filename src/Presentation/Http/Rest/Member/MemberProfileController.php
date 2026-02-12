<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Member;

use App\Application\Member\Command\UpdateMemberNamesCommand;
use App\Application\Member\Command\UpdateMemberNamesHandler;
use App\Application\Member\Command\UpdateMemberPasswordCommand;
use App\Application\Member\Command\UpdateMemberPasswordHandler;
use App\Application\Member\Command\UpdateMemberSurnamesCommand;
use App\Application\Member\Command\UpdateMemberSurnamesHandler;
use App\Application\Member\Query\GetMemberProfileHandler;
use App\Application\Member\Query\GetMemberProfileQuery;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/names', name: 'member_profile_update_names', methods: ['PATCH'])]
    public function updateNames(
        Request $request,
        #[CurrentUser] User $user,
        UpdateMemberNamesHandler $handler
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $command = new UpdateMemberNamesCommand(
                userId: (string)$user->getId(),
                name: $data['name'] ?? ''
            );

            $handler($command);

            return $this->json(['message' => 'Names updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/surnames', name: 'member_profile_update_surnames', methods: ['PATCH'])]
    public function updateSurnames(
        Request $request,
        #[CurrentUser] User $user,
        UpdateMemberSurnamesHandler $handler
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $command = new UpdateMemberSurnamesCommand(
                userId: (string)$user->getId(),
                surname: $data['surname'] ?? ''
            );

            $handler($command);

            return $this->json(['message' => 'Surnames updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/password', name: 'member_profile_update_password', methods: ['PATCH'])]
    public function updatePassword(
        Request $request,
        #[CurrentUser] User $user,
        UpdateMemberPasswordHandler $handler
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $command = new UpdateMemberPasswordCommand(
                userId: (string)$user->getId(),
                currentPassword: $data['current_password'] ?? '',
                newPassword: $data['new_password'] ?? ''
            );

            $handler($command);

            return $this->json(['message' => 'Password updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
