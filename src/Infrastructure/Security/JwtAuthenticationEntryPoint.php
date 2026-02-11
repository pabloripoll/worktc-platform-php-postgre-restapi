<?php

namespace App\Infrastructure\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class JwtAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        $message = $authException ? $authException->getMessage() : 'Authentication Required.';

        return new JsonResponse([
            'status' => 401,
            'error' => 'unauthenticated',
            'message' => $message
        ], 401);
    }
}
