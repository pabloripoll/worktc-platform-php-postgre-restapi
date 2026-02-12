<?php

namespace App\Infrastructure\Event\Listener;

use App\Domain\User\ValueObject\UserRole;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    public function __construct(private RequestStack $requestStack) {}

    /**
     * JWT Custom Payload
     *
     * Adds custom claims but no any sensitive data like password
     */
    public function onJwtCreated(JWTCreatedEvent $event): void
    {
        /** @var \App\Domain\User\Entity\User $user */
        $user = $event->getUser();
        $role = $user->getRole();

        // gets the payload array that will be encoded
        $payload = $event->getData();

        $payload['user_id'] = $user->getId();

        if ($role === UserRole::ADMIN) {
            $payload['is_admin'] = true;
        }

        if ($role === UserRole::MEMBER) {
            $payload['is_member'] = true;
        }

        // Optionally include request info (ip, etc.)
        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            $payload['ip'] = $request->getClientIp();
        }

        // Set back modified payload
        $event->setData($payload);
    }
}
