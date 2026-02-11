<?php

namespace App\Presentation\Http\Rest\Member;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use App\Domain\Member\Repository\MemberRepository;
use App\Domain\Member\Repository\MemberAccessLogRepository;
use App\Domain\Member\Repository\MemberActivationCodeRepository;
use App\Domain\Member\Repository\MemberProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Messenger\MessageBusInterface;

#[Route('/api/v1/auth')]
class MemberAuthController extends AbstractController
{
    private int $jwtTime = 60;

    public function __construct(
        private EntityManagerInterface $em,
        private JWTTokenManagerInterface $jwtManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
        private UserRepository $userRepo,
        private MemberRepository $memberRepo,
        private MemberProfileRepository $profileRepo,
        private MemberActivationCodeRepository $activationRepo,
        private MemberAccessLogRepository $accessLogRepo,
        private MessageBusInterface $messageBus,
    ) {}

    #[Route('/login', name: 'api_member_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = [];
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is required and must be valid.';
        }
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required.';
        }
        if ($errors) {
            $firstField = array_key_first($errors);
            return new JsonResponse(['message' => $errors[$firstField], 'error' => $firstField], JsonResponse::HTTP_UNAUTHORIZED);
        }

        /* @var \App\Domain\User\Entity\User $user */
        $user = $this->userRepo->findOneByEmail($data['email']);
        if (
            ! $user ||
            ! $this->passwordHasher->isPasswordValid($user, $data['password']) ||
            ! in_array('ROLE_MEMBER', $user->getRoles(), true)
        ) {
            return new JsonResponse(['message' => 'Invalid credentials.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // From now on, output is managed by ./src/Security/CustomAuthenticationSuccessHandler.php
        return new JsonResponse([], JsonResponse::HTTP_OK);
    }

    #[Route('/refresh', name: 'api_member_refresh', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
        if (! $token) {
            return new JsonResponse(['message' => 'Token not provided.', 'error' => 'token_not_provided'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $accessToken = $this->accessLogRepo->findOneBy(['token' => $token]);
        if (! $accessToken) {
            return new JsonResponse(['message' => 'Token not registered.', 'error' => 'token_not_found'], JsonResponse::HTTP_NOT_FOUND);
        }
        if ($accessToken->getIsTerminated()) {
            return new JsonResponse(['message' => 'Token cannot be refreshed.', 'error' => 'token_terminated'], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // LexikJWT does not provide a refresh mechanism by default, this implementation re-issues the token
        $user = $accessToken->getUser();
        $legacyAccessTokenCeasedAt = $accessToken->getExpiresAt();
        $refreshedToken = $this->jwtManager->create($user);

        $accessToken->setExpiresAt((new \DateTime())->modify("+{$this->jwtTime} minutes"));
        $accessToken->setRefreshCount($accessToken->getRefreshCount() + 1);
        $accessToken->setToken($refreshedToken);
        $this->em->flush();

        return new JsonResponse([
            'token' => $accessToken->getToken(),
            'expires_in' => $this->jwtTime * 60,
            'token_expired' => $token,
            'token_expired_ceased' => $legacyAccessTokenCeasedAt,
        ], JsonResponse::HTTP_ACCEPTED);

        return new JsonResponse(['user' => $user->getId()], JsonResponse::HTTP_OK);
    }

    #[Route('/logout', name: 'api_member_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
        if (!$token) {
            return new JsonResponse(['message' => 'Token not provided.', 'error' => 'token_not_provided'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $accessToken = $this->accessLogRepo->findOneBy(['token' => $token]);
        if (!$accessToken) {
            return new JsonResponse(['message' => 'Token not registered.', 'error' => 'token_not_found'], JsonResponse::HTTP_NOT_FOUND);
        }
        if ($accessToken->getIsTerminated()) {
            return new JsonResponse(['message' => 'Token is already terminated.', 'error' => 'token_terminated'], JsonResponse::HTTP_NOT_MODIFIED);
        }

        $accessToken->setIsTerminated(true);
        $this->em->flush();

        // LexikJWT does not have a built-in invalidate, but you can blacklist tokens if enabled

        return new JsonResponse(['token_expired' => $token], JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/whoami', name: 'api_member_whoami', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function whoami(#[CurrentUser] User $user): JsonResponse
    {
        $member = $this->memberRepo->findOneBy(['user' => $user]);
        $profile = $this->profileRepo->findOneBy(['user' => $user]);

        return new JsonResponse([
            'email' => $user->getEmail(),
            'uid' => $member ? $member->getUid() : null,
            'nickname' => $profile ? $profile->getNickname() : null,
            'avatar' => $profile ? $profile->getAvatar() : null,
        ], JsonResponse::HTTP_OK);
    }
}
