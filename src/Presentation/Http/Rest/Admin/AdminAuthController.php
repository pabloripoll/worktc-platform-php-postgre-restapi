<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Admin;

use App\Application\User\Command\RegisterMemberCommand;
use App\Application\User\Command\RegisterMemberHandler;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\User\Entity\User;
use App\Presentation\Request\Admin\CreateMemberRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/admin/auth')]
class AdminAuthController extends AbstractController
{
    /**
     * Login handled by security.yaml firewall + CustomAuthenticationSuccessHandler
     */
    #[Route('/login', name: 'admin_auth_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // This method is never called - security.yaml handles login
        return $this->json([]);
    }

    /**
     * Admin creates a new member account
     */
    #[Route('/register-member', name: 'admin_auth_register_member', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function registerMember(
        CreateMemberRequest $request,
        #[CurrentUser] User $currentUser,
        RegisterMemberHandler $handler
    ): JsonResponse {
        $errors = $request->validate();
        if (!empty($errors)) {
            return $this->json([
                'error' => 'Validation failed',
                'violations' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $command = new RegisterMemberCommand(
                email: $request->email,
                password: $request->password,
                name: $request->name,
                surname: $request->surname,
                phoneNumber: $request->phone_number ?? null,
                department: $request->department ?? null,
                birthDate: $request->birth_date ?? null,
                createdByUserId: (string)$currentUser->getId()
            );

            $userId = $handler($command);

            return $this->json([
                'user_id' => (string)$userId,
                'email' => $request->email,
                'message' => 'Member created successfully'
            ], Response::HTTP_CREATED);

        } catch (DomainException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
