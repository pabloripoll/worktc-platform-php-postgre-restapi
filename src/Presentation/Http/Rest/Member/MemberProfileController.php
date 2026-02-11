<?php

namespace App\Presentation\Http\Rest\Member;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MemberProfileController extends AbstractController
{
    #[Route('/api/v1/members', name: 'member_list_sections', methods: ['GET'])]
    public function listSections(Request $request): JsonResponse
    {
        $response = ['test' => true];

        return $this->json($response, JsonResponse::HTTP_OK);
    }

    #[Route('/api/v1/members/{user_uid}/profile', name: 'member_read_profile', methods: ['GET'])]
    public function readProfile(Request $request, string $user_uid): JsonResponse
    {
        $response = ['test' => true, 'user_uid' => $user_uid];

        return $this->json($response, JsonResponse::HTTP_OK);
    }

    #[Route('/api/v1/members/{user_uid}/posts', name: 'member_listing_posts', methods: ['GET'])]
    public function listingPosts(Request $request, string $user_uid): JsonResponse
    {
        $response = ['test' => true, 'user_uid' => $user_uid];

        return $this->json($response, JsonResponse::HTTP_OK);
    }
}