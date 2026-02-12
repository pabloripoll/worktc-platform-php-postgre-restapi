<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/auth')]
class MemberAuthController extends AbstractController
{
    /**
     * Login handled by security.yaml firewall + CustomAuthenticationSuccessHandler
     */
    #[Route('/login', name: 'api_member_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // This method is never called - security.yaml handles login
        // The firewall intercepts and processes the login request
        return $this->json([]);
    }
}
