<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\User;

use App\Application\User\Query\GetUserByIdHandler;
use App\Application\User\Query\GetUserByIdQuery;
use App\Domain\Admin\Repository\AdminAccessLogRepositoryInterface;
use App\Domain\Member\Repository\MemberAccessLogRepositoryInterface;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\User\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/auth')]
class UserAuthController extends AbstractController
{
    private int $jwtTtl = 3600;

    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private AdminAccessLogRepositoryInterface $adminAccessLogRepo,
        private MemberAccessLogRepositoryInterface $memberAccessLogRepo
    ) {}

    #[Route('/refresh', name: 'api_user_refresh', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function refresh(Request $request, #[CurrentUser] User $user): JsonResponse
    {
        try {
            $token = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
            if ($token === null) {
                return $this->json(['message' => 'Token not provided', 'error' => 'token_not_provided'], JsonResponse::HTTP_UNAUTHORIZED);
            }

            // Find access log based on user role
            $accessLog = $user->isAdmin()
                ? $this->adminAccessLogRepo->findByToken($token)
                : $this->memberAccessLogRepo->findByToken($token);

            if ($accessLog === null) {
                return $this->json(['message' => 'Token not registered', 'error' => 'token_not_found'], JsonResponse::HTTP_NOT_FOUND);
            }

            if ($accessLog->isTerminated()) {
                return $this->json(['message' => 'Token terminated', 'error' => 'token_terminated'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Generate new token
            $newToken = $this->jwtManager->create($user);
            $newExpiresAt = (new \DateTimeImmutable())->modify("+{$this->jwtTtl} seconds");

            // Update access log
            $accessLog->refresh($newToken, $newExpiresAt);

            if ($user->isAdmin()) {
                $this->adminAccessLogRepo->save($accessLog);
            } else {
                $this->memberAccessLogRepo->save($accessLog);
            }

            return $this->json([
                'token' => $newToken,
                'expires_in' => $this->jwtTtl,
                'token_expired' => $token,
            ], JsonResponse::HTTP_ACCEPTED);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/logout', name: 'api_user_logout', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function logout(Request $request, #[CurrentUser] User $user): JsonResponse
    {
        try {
            $token = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
            if ($token === null) {
                return $this->json(['message' => 'Token not provided'], JsonResponse::HTTP_UNAUTHORIZED);
            }

            $accessLog = $user->isAdmin()
                ? $this->adminAccessLogRepo->findByToken($token)
                : $this->memberAccessLogRepo->findByToken($token);

            if ($accessLog === null) {
                return $this->json(['message' => 'Token not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            if ($accessLog->isTerminated()) {
                return $this->json(['message' => 'Token already terminated'], JsonResponse::HTTP_NOT_MODIFIED);
            }

            $accessLog->terminate();

            if ($user->isAdmin()) {
                $this->adminAccessLogRepo->save($accessLog);
            } else {
                $this->memberAccessLogRepo->save($accessLog);
            }

            return $this->json(['token_expired' => $token], JsonResponse::HTTP_ACCEPTED);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/whoami', name: 'api_user_whoami', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function whoami(
        #[CurrentUser] User $user,
        GetUserByIdHandler $handler
    ): JsonResponse {
        try {
            $query = new GetUserByIdQuery((string)$user->getId());
            $userDTO = (object) $handler($query);

            return $this->json([
                'id' => $userDTO->id,
                'email' => $userDTO->email,
                'role' => $userDTO->role,
                'name' => $userDTO->name,
                'surname' => $userDTO->surname,
                'phone_number' => $userDTO->phoneNumber,
                'department' => $userDTO->department,
            ], JsonResponse::HTTP_OK);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
