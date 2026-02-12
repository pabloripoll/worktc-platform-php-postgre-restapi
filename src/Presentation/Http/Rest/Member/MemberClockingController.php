<?php

declare(strict_types=1);

namespace App\Presentation\Http\Rest\Member;

use App\Application\WorkEntry\Command\CreateWorkEntryCommand;
use App\Application\WorkEntry\Command\CreateWorkEntryHandler;
use App\Application\WorkEntry\Command\DeleteWorkEntryCommand;
use App\Application\WorkEntry\Command\DeleteWorkEntryHandler;
use App\Application\WorkEntry\Command\UpdateWorkEntryCommand;
use App\Application\WorkEntry\Command\UpdateWorkEntryHandler;
use App\Application\WorkEntry\Query\GetWorkEntriesByUserHandler;
use App\Application\WorkEntry\Query\GetWorkEntriesByUserQuery;
use App\Application\WorkEntry\Query\GetWorkEntryByIdHandler;
use App\Application\WorkEntry\Query\GetWorkEntryByIdQuery;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/clockings')]
#[IsGranted('ROLE_MEMBER')]
class MemberClockingController extends AbstractController
{
    #[Route('', name: 'member_clocking_create', methods: ['POST'])]
    public function create(
        Request $request,
        #[CurrentUser] User $user,
        CreateWorkEntryHandler $handler
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $command = new CreateWorkEntryCommand(
                userId: (string)$user->getId(),
                startDate: $data['start_date'] ?? (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
                endDate: $data['end_date'] ?? null,
                createdByUserId: (string)$user->getId()
            );

            $workEntryId = $handler($command);

            return $this->json([
                'id' => (string)$workEntryId,
                'message' => 'Clocking created successfully'
            ], JsonResponse::HTTP_CREATED);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'member_clocking_list', methods: ['GET'])]
    public function list(
        #[CurrentUser] User $user,
        GetWorkEntriesByUserHandler $handler
    ): JsonResponse {
        try {
            $query = new GetWorkEntriesByUserQuery((string)$user->getId());
            $workEntries = $handler($query);

            return $this->json($workEntries);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'member_clocking_get', methods: ['GET'])]
    public function get(
        string $id,
        #[CurrentUser] User $user,
        GetWorkEntryByIdHandler $handler
    ): JsonResponse {
        try {
            $query = new GetWorkEntryByIdQuery($id, (string)$user->getId());
            $workEntry = $handler($query);

            return $this->json($workEntry);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    #[Route('/{id}', name: 'member_clocking_update', methods: ['PATCH'])]
    public function update(
        string $id,
        Request $request,
        #[CurrentUser] User $user,
        UpdateWorkEntryHandler $handler
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $command = new UpdateWorkEntryCommand(
                workEntryId: $id,
                userId: (string)$user->getId(),
                startDate: $data['start_date'] ?? null,
                endDate: $data['end_date'] ?? null,
                updatedByUserId: (string)$user->getId()
            );

            $handler($command);

            return $this->json(['message' => 'Clocking updated successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'member_clocking_delete', methods: ['DELETE'])]
    public function delete(
        string $id,
        #[CurrentUser] User $user,
        DeleteWorkEntryHandler $handler
    ): JsonResponse {
        try {
            $command = new DeleteWorkEntryCommand(
                workEntryId: $id,
                userId: (string)$user->getId(),
                deletedByUserId: (string)$user->getId()
            );

            $handler($command);

            return $this->json(['message' => 'Clocking deleted successfully']);
        } catch (DomainException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
