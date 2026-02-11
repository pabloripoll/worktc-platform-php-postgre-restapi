<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class ApiAccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException): JsonResponse
    {
        $message = $accessDeniedException->getMessage() ?: 'Access denied.';

        return new JsonResponse([
            'status' => 403,
            'error' => 'forbidden',
            'message' => $message
        ], 403);
    }
}
