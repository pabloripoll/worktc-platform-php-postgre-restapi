<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Admin\Entity\AdminAccessLog;
use App\Domain\Admin\Repository\AdminAccessLogRepositoryInterface;
use App\Domain\Member\Entity\MemberAccessLog;
use App\Domain\Member\Repository\MemberAccessLogRepositoryInterface;
use App\Domain\User\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final class CustomAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private int $jwtTtl = 3600;

    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private AdminAccessLogRepositoryInterface $adminAccessLogRepo,
        private MemberAccessLogRepositoryInterface $memberAccessLogRepo
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        /** @var User $user */
        $user = $token->getUser();

        // Generate JWT
        $jwtToken = $this->jwtManager->create($user);
        $expiresAt = (new \DateTimeImmutable())->modify("+{$this->jwtTtl} seconds");

        // Log access
        $ipAddress = $request->getClientIp();
        $userAgent = $request->headers->get('User-Agent');

        if ($user->isAdmin()) {
            $accessLog = AdminAccessLog::create(
                $user->getId(),
                $jwtToken,
                $expiresAt,
                $ipAddress,
                $userAgent
            );
            $this->adminAccessLogRepo->save($accessLog);
        } else {
            $accessLog = MemberAccessLog::create(
                $user->getId(),
                $jwtToken,
                $expiresAt,
                $ipAddress,
                $userAgent
            );
            $this->memberAccessLogRepo->save($accessLog);
        }

        return new JsonResponse([
            'token' => $jwtToken,
            'expires_in' => $this->jwtTtl,
        ], JsonResponse::HTTP_ACCEPTED);
    }
}
