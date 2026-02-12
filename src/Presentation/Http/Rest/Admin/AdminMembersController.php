<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Admin;

use App\Application\Member\Command\UpdateMemberProfileCommand;
use App\Application\Member\Command\UpdateMemberProfileHandler;
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
use App\Domain\User\ValueObject\UserRole;
use App\Presentation\Request\Admin\CreateMemberRequest;
use App\Presentation\Request\Admin\UpdateMemberProfileRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin/members')]
#[IsGranted('ROLE_ADMIN')]
class AdminMembersController extends AbstractController
{
    #[Route('', name: 'admin_members_create', methods: ['POST'])]
    public function create(
        Request $request,  // ✅ Use plain Request instead
        #[CurrentUser] User $currentUser,
        RegisterMemberHandler $handler
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            if (!is_array($data)) {
                return $this->json([
                    'error' => 'Invalid JSON'
                ], Response::HTTP_BAD_REQUEST);
            }

            $errors = [];
            if (empty($data['email'])) {
                $errors['email'] = 'Email is required';
            }
            if (empty($data['password'])) {
                $errors['password'] = 'Password is required';
            }
            if (empty($data['name'])) {
                $errors['name'] = 'Name is required';
            }
            if (empty($data['surname'])) {
                $errors['surname'] = 'Surname is required';
            }

            if (!empty($errors)) {
                return $this->json([
                    'error' => 'Validation failed',
                    'violations' => $errors
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new RegisterMemberCommand(
                email: $data['email'],
                password: $data['password'],
                name: $data['name'],
                surname: $data['surname'],
                createdByUserId: (string)$currentUser->getId(),
                phoneNumber: $data['phone_number'] ?? null,
                department: $data['department'] ?? null,
                birthDate: $data['birth_date'] ?? null,
            );

            $userId = $handler($command);

            return $this->json([
                'user_id' => (string)$userId,
                'message' => 'Member created successfully'
            ], Response::HTTP_CREATED);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'admin_members_list', methods: ['GET'])]
    public function list(
        Request $request,
        GetAllUsersHandler $handler
    ): JsonResponse {
        try {
            $pagination = PaginationDTO::fromRequest($request->query->all());
            $query = new GetAllUsersQuery(UserRole::MEMBER, $pagination);
            $result = $handler($query);

            return $this->json($result->toArray());
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
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
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/{id}/profiles', name: 'admin_members_update_profile', methods: ['PATCH'])]
    public function updateProfile(
        string $id,
        Request $request,
        UpdateMemberProfileHandler $handler
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            if (!is_array($data)) {
                return $this->json([
                    'error' => 'Invalid JSON'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new UpdateMemberProfileCommand(
                userId: $id,
                name: $data['name'] ?? null,
                surname: $data['surname'] ?? null,
                phoneNumber: $data['phone_number'] ?? null,
                department: $data['department'] ?? null,
                birthDate: $data['birth_date'] ?? null,
                currentPassword: $data['current_password'] ?? null,
                newPassword: $data['new_password'] ?? null,
            );

            $handler($command);

            return $this->json([
                'message' => 'Member profile updated successfully'
            ]);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
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

            if ($user === null) {
                return $this->json([
                    'error' => 'User not found'
                ], Response::HTTP_NOT_FOUND);
            }

            if (!$user->isMember()) {
                return $this->json([
                    'error' => 'User is not a member'
                ], Response::HTTP_BAD_REQUEST);
            }

            $user->softDelete();
            $userRepository->save($user);

            return $this->json([
                'message' => 'Member profile deleted successfully'
            ]);
        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
