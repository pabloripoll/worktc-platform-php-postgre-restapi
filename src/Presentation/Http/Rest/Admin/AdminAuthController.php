<?php

namespace App\Presentation\Http\Rest\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Admin Authentication",
 *     description="Endpoints about the authenticated user"
 * )
 */
class AdminAuthController extends AbstractController
{
    // JWT access expiration, smaller than JWT TTL config
    private int $jwtTime = 60;

    /**
     * Register a new member
     *
     * @OA\Post(
     *     path="/api/v1/admin/auth/register",
     *     summary="Register a new member",
     *     tags={"Admin Authentication"},
     *     description="Registers a new member account and returns basic profile info and the activation code.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password","nickname"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="yourPassword123"),
     *             @OA\Property(property="nickname", type="string", example="JohnDoe"),
     *             @OA\Property(property="region_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             @OA\Property(property="uid", type="integer", example=156490),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="nickname", type="string", example="JohnDoe"),
     *             @OA\Property(property="activation_code", type="string", example="A1B2C3")
     *         )
     *     ),
     *     @OA\Response(
     *         response=406,
     *         description="Validation error"
     *     )
     * )
    */
    #[Route('/api/v1/admin/auth/register', name: 'admin_auth_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        // Implement: Validate $request, create User, Admin, Activation, Profile, etc.
        // Return structure as in Laravel: uid, email, nickname, activation_code
        return $this->json(
            [
                'uid' => 156490,
                'email' => 'john@example.com',
                'nickname' => 'JohnDoe',
                'activation_code' => 'A1B2C3'
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    #[Route('/api/v1/admin/auth/activation', name: 'admin_auth_activation', methods: ['POST'])]
    public function activation(Request $request): JsonResponse
    {
        // Implement: Validate $request, activate user account
        return $this->json(
            [
                'email' => 'john@example.com',
                'status' => 'Account activation has been activated.'
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }

    #[Route('/api/v1/admin/auth/login', name: 'admin_auth_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // Implement: Validate credentials, check activation, JWT creation, log access
        return $this->json(
            [
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGci...',
                'expires_in' => 3600
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }

    #[Route('/api/v1/admin/auth/refresh', name: 'admin_auth_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        // Implement: Validate JWT, refresh token, update access log
        return $this->json(
            [
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGci...',
                'token_expired' => 'eyJ0eXAiOiJKV1QiLCJhbGci...',
                'expires_in' => 3600
            ],
            JsonResponse::HTTP_ACCEPTED
        );
    }

    #[Route('/api/v1/admin/auth/logout', name: 'admin_auth_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        // Implement: Validate JWT, mark token as terminated
        return $this->json(
            ['token_expired' => 'eyJ0eXAiOiJKV1QiLCJhbGci...'],
            JsonResponse::HTTP_ACCEPTED
        );
    }

    #[Route('/api/v1/admin/auth/whoami', name: 'admin_auth_whoami', methods: ['GET'])]
    public function whoami(Request $request): JsonResponse
    {
        // Implement: Get authenticated user info
        return $this->json(
            [
                'email' => 'john@example.com',
                'uid' => 156490,
                'nickname' => 'JohnDoe',
                'avatar' => 'http://...'
            ],
            JsonResponse::HTTP_OK
        );
    }
}
