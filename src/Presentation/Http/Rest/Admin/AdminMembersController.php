<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Admin;

use App\Application\Member\Command\UpdateMemberNamesCommand;
use App\Application\Member\Command\UpdateMemberNamesHandler;
use App\Application\Member\Command\UpdateMemberPasswordCommand;
use App\Application\Member\Command\UpdateMemberPasswordHandler;
use App\Application\Member\Command\UpdateMemberSurnamesCommand;
use App\Application\Member\Command\UpdateMemberSurnamesHandler;
use App\Application\Shared\DTO\PaginationDTO;
use App\Application\User\Command\RegisterMemberCommand;
use App\Application\User\Command\RegisterMemberHandler;
use App\Application\User\Query\GetAllUsersHandler;
use App\Application\User\Query\GetAllUsersQuery;
use App\Application\User\Query\GetUserByIdHandler;
use App\Application\User\Query\GetUserByIdQuery;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Presentation\Request\Admin\CreateMemberRequest;
use App\Presentation\Request\Admin\UpdatePasswordRequest;
use App\Presentation\Request\Admin\UpdateProfileNamesRequest;
use App\Presentation\Request\Admin\UpdateProfileSurnamesRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin/members')]
#[IsGranted('ROLE_ADMIN')]
class AdminMembersController extends AbstractController
{
    #[Route('', name: 'admin_members_create', methods: ['POST'])]
    public function create(
        CreateMemberRequest $request,
        #[CurrentUser] User $currentUser,
        RegisterMemberHandler $handler
    ): JsonResponse {
        try {
            $command = new RegisterMemberCommand(
                email: $request->email,
                password: $request->password,
                name: $request->name,
                surname: $request->surname,
                phoneNumber: $request->phoneNumber,
                department: $request->department,
                birthDate: $request->birthDate,
                createdByUserId: (string)$currentUser->getId()
            );

            $userId = $handler($command);

            return $this->json([
                'user_id' => (string)$userId,
                'message' => 'Member created successfully'
            ], JsonResponse::HTTP_CREATED);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'admin_members_list', methods: ['GET'])]
    public function list(
        Request $request,
        GetAllUsersHandler $handler
    ): JsonResponse {
        try {
            $pagination = PaginationDTO::fromRequest($request->query->all());

            $query = new GetAllUsersQuery('ROLE_MEMBER', $pagination);
            $result = $handler($query);

            return $this->json($result->toArray());
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/profiles', name: 'admin_members_get_profile', methods: ['GET'])]
    public function getProfile(
        string $id,
        GetUserByIdHandler $handler
    ): JsonResponse {
        try {
            $query = new GetUserByIdQuery($id);
            $user = $handler($query);

            return $this->json($user);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    #[Route('/{id}/profiles', name: 'admin_members_delete_profile', methods: ['DELETE'])]
    public function deleteProfile(
        string $id,
        UserRepositoryInterface $userRepository
    ): JsonResponse {
        try {
            $userId = Uuid::fromString($id);
            $user = $userRepository->findById($userId);

            if (!$user) {
                return $this->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            if (!$user->isMember()) {
                return $this->json(['error' => 'User is not a member'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $user->softDelete();
            $userRepository->save($user);

            return $this->json(['message' => 'Member profile deleted successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/profiles/names', name: 'admin_members_update_names', methods: ['PATCH'])]
    public function updateNames(
        string $id,
        UpdateProfileNamesRequest $request,
        UpdateMemberNamesHandler $handler
    ): JsonResponse {
        try {
            $command = new UpdateMemberNamesCommand(
                userId: $id,
                name: $request->name
            );

            $handler($command);

            return $this->json(['message' => 'Member names updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/profiles/surnames', name: 'admin_members_update_surnames', methods: ['PATCH'])]
    public function updateSurnames(
        string $id,
        UpdateProfileSurnamesRequest $request,
        UpdateMemberSurnamesHandler $handler
    ): JsonResponse {
        try {
            $command = new UpdateMemberSurnamesCommand(
                userId: $id,
                surname: $request->surname
            );

            $handler($command);

            return $this->json(['message' => 'Member surnames updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/profiles/password', name: 'admin_members_update_password', methods: ['PATCH'])]
    public function updatePassword(
        string $id,
        UpdatePasswordRequest $request,
        UpdateMemberPasswordHandler $handler
    ): JsonResponse {
        try {
            $command = new UpdateMemberPasswordCommand(
                userId: $id,
                currentPassword: $request->currentPassword,
                newPassword: $request->newPassword
            );

            $handler($command);

            return $this->json(['message' => 'Member password updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
