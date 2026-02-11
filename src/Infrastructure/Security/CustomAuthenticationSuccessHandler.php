<?php

namespace App\Infrastructure\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use App\Domain\Admin\Entity\AdminAccessLog;
use App\Domain\Member\Entity\MemberAccessLog;
use App\Domain\Member\Repository\MemberActivationCodeRepository;

class CustomAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private JWTTokenManagerInterface $jwtManager;
    private int $tokenTtl;

    public function __construct(
        private EntityManagerInterface $em,
        private MemberActivationCodeRepository $activationRepo,
        JWTTokenManagerInterface $jwtManager,
        int $tokenTtl = 3600
        )
    {
        $this->jwtManager = $jwtManager;
        $this->tokenTtl = $tokenTtl;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();
        $jwt = $this->jwtManager->create($user);

        /** @var \App\Domain\User\Entity\User $user */
        $role = $user->getRole();

        if ($role == 'ROLE_ADMIN') {
            $accessLog = new AdminAccessLog();
        }

        if ($role == 'ROLE_MEMBER') {
            $requiresActivation = (bool) ($_ENV['LOGIN_ACTIVATION_CODE'] ?? false);
            $activation = $this->activationRepo->findOneBy(['user' => $user, 'is_active' => true]);
            if ($requiresActivation && !$activation) {
                return new JsonResponse(['message' => 'Access requires activation.'], JsonResponse::HTTP_UNAUTHORIZED);
            }

            $accessLog = new MemberAccessLog();
        }

        $accessLog->setUser($user);
        $accessLog->setToken($jwt);
        $accessLog->setExpiresAt((new \DateTime())->modify("+{$this->tokenTtl} minutes"));
        $accessLog->setIpAddress($request->getClientIp());
        $accessLog->setUserAgent($request->headers->get('User-Agent'));
        $accessLog->setRequestsCount(1);
        $accessLog->setPayload([]);
        $this->em->persist($accessLog);
        $this->em->flush();

        return new JsonResponse([
            'token' => $jwt,
            'expires_in' => $this->tokenTtl,
        ], JsonResponse::HTTP_ACCEPTED);
    }
}
