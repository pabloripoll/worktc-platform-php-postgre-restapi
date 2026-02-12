<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Admin;

use App\Application\Shared\DTO\PaginationDTO;
use App\Application\User\Command\RegisterAdminCommand;
use App\Application\User\Command\RegisterAdminHandler;
use App\Application\User\Query\GetAllUsersHandler;
use App\Application\User\Query\GetAllUsersQuery;
use App\Application\User\Query\GetUserByIdHandler;
use App\Application\User\Query\GetUserByIdQuery;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Presentation\Request\Admin\CreateAdminRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Domain\User\ValueObject\UserRole;

#[Route('/api/v1/admin/users')]
#[IsGranted(UserRole::ADMIN)]
class AdminUsersController extends AbstractController
{
    #[Route('', name: 'admin_users_create', methods: ['POST'])]
    public function create(
        CreateAdminRequest $request,
        #[CurrentUser] User $currentUser,
        RegisterAdminHandler $handler
    ): JsonResponse {
        try {
            $command = new RegisterAdminCommand(
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
                'message' => 'Admin user created successfully'
            ], JsonResponse::HTTP_CREATED);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'admin_users_list', methods: ['GET'])]
    public function list(
        Request $request,
        GetAllUsersHandler $handler
    ): JsonResponse {
        try {
            $pagination = PaginationDTO::fromRequest($request->query->all());

            $query = new GetAllUsersQuery(UserRole::ADMIN, $pagination);
            $result = $handler($query);

            return $this->json($result->toArray());
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/profiles', name: 'admin_users_get_profile', methods: ['GET'])]
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

    #[Route('/{id}', name: 'admin_users_delete', methods: ['DELETE'])]
    public function delete(
        string $id,
        UserRepositoryInterface $userRepository
    ): JsonResponse {
        try {
            $userId = Uuid::fromString($id);
            $user = $userRepository->findById($userId);

            if ($user === null) {
                return $this->json(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            if (!$user->isAdmin()) {
                return $this->json(['error' => 'User is not an admin'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $user->softDelete();
            $userRepository->save($user);

            return $this->json(['message' => 'Admin user deleted successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
