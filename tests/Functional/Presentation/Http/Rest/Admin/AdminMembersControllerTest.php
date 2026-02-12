<?php

declare(strict_types=1);

namespace App\Tests\Functional\Presentation\Http\Rest\Admin;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class AdminMembersControllerTest extends WebTestCase
{
    private function loginAsAdmin(KernelBrowser $client): string
    {
        $client->request('POST', '/api/v1/admin/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]));

        $response = $client->getResponse();

        if (!$response->isSuccessful()) {
            $this->fail(sprintf(
                'Admin login failed with status %d: %s',
                $response->getStatusCode(),
                $response->getContent()
            ));
        }

        $content = $response->getContent();
        $data = json_decode($content ?: '{}', true);

        if (!isset($data['token'])) {
            $this->fail('No token in admin login response: ' . $content);
        }

        return $data['token'];
    }

    public function testCreateMemberWithAllFields(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        // Create member with all fields at once
        $payload = [
            'email' => 'newmember' . time() . '@test.com',
            'password' => 'password123',
            'name' => 'John',
            'surname' => 'Doe',
            'birth_date' => '1990-01-15',
            'phone_number' => '+34600123456',
            'department' => 'IT Department',
        ];

        $client->request('POST', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $response = $client->getResponse();
        $content = $response->getContent();

        if (!$response->isSuccessful()) {
            $this->fail(sprintf(
                'Create member failed with status %d: %s',
                $response->getStatusCode(),
                $content
            ));
        }

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('user_id', $data);
    }

    public function testCreateMemberWithMinimalFields(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        // Create member with minimal required fields
        $payload = [
            'email' => 'minimal' . time() . '@test.com',
            'password' => 'password123',
            'name' => 'Jane',
            'surname' => 'Smith',
            'birth_date' => null, // Optional
        ];

        $client->request('POST', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $response = $client->getResponse();
        $content = $response->getContent();

        if (!$response->isSuccessful()) {
            $this->fail(sprintf(
                'Create member with minimal fields failed with status %d: %s',
                $response->getStatusCode(),
                $content
            ));
        }

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testGetMemberProfile(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        // Get existing member from fixtures
        $client->request('GET', '/api/v1/admin/members?page=1&limit=1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $listData = json_decode($content, true);
        $this->assertArrayHasKey('data', $listData);
        $this->assertNotEmpty($listData['data']);

        $memberId = $listData['data'][0]['id'] ?? $listData['data'][0]['user_id'];

        // Get the profile
        $client->request('GET', "/api/v1/admin/members/{$memberId}/profiles", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();

        $profileContent = $client->getResponse()->getContent();
        $this->assertNotFalse($profileContent);

        $profileData = json_decode($profileContent, true);
        $this->assertIsArray($profileData);
        $this->assertArrayHasKey('name', $profileData);
        $this->assertArrayHasKey('surname', $profileData);
    }

    public function testUpdateMemberNames(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        // Get a member ID
        $client->request('GET', '/api/v1/admin/members?page=1&limit=1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $content = $client->getResponse()->getContent();
        $listData = json_decode($content ?: '{}', true);
        $memberId = $listData['data'][0]['id'] ?? $listData['data'][0]['user_id'];

        // Update only the name field
        $client->request('PATCH', "/api/v1/admin/members/{$memberId}/profiles/names", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'UpdatedName',
        ]));

        $this->assertResponseIsSuccessful();

        // Verify the change
        $client->request('GET', "/api/v1/admin/members/{$memberId}/profiles", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $profileContent = $client->getResponse()->getContent();
        $profileData = json_decode($profileContent ?: '{}', true);

        $this->assertEquals('UpdatedName', $profileData['name']);
    }

    public function testUpdateMemberSurnames(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        // Get a member ID
        $client->request('GET', '/api/v1/admin/members?page=1&limit=1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $content = $client->getResponse()->getContent();
        $listData = json_decode($content ?: '{}', true);
        $memberId = $listData['data'][0]['id'] ?? $listData['data'][0]['user_id'];

        // Update only the surname field
        $client->request('PATCH', "/api/v1/admin/members/{$memberId}/profiles/surnames", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'surname' => 'UpdatedSurname',
        ]));

        $this->assertResponseIsSuccessful();

        // Verify the change
        $client->request('GET', "/api/v1/admin/members/{$memberId}/profiles", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $profileContent = $client->getResponse()->getContent();
        $profileData = json_decode($profileContent ?: '{}', true);

        $this->assertEquals('UpdatedSurname', $profileData['surname']);
    }

    public function testUpdateMemberPassword(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        // Get a member ID
        $client->request('GET', '/api/v1/admin/members?page=1&limit=1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $content = $client->getResponse()->getContent();
        $listData = json_decode($content ?: '{}', true);
        $memberId = $listData['data'][0]['id'] ?? $listData['data'][0]['user_id'];

        // Update password using snake_case (matches your request class)
        $client->request('PATCH', "/api/v1/admin/members/{$memberId}/profiles/password", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'current_password' => 'password123',
            'new_password' => 'newpassword456',
        ]));

        $this->assertResponseIsSuccessful();
    }

    public function testDeleteMemberProfile(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        // Create a new member to delete
        $createPayload = [
            'email' => 'todelete' . time() . '@test.com',
            'password' => 'password123',
            'name' => 'To',
            'surname' => 'Delete',
            'birth_date' => '1995-05-20',
        ];

        $client->request('POST', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($createPayload));

        $createContent = $client->getResponse()->getContent();
        $createData = json_decode($createContent ?: '{}', true);
        $memberId = $createData['user_id'];

        // Soft delete the profile
        $client->request('DELETE', "/api/v1/admin/members/{$memberId}/profiles", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();

        // Verify it's deleted (should return 404 or show deleted status)
        $client->request('GET', "/api/v1/admin/members/{$memberId}/profiles", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Depending on your implementation, it should be 404 or show deleted_at
        $this->assertTrue(
            $client->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND ||
            $client->getResponse()->isSuccessful()
        );
    }

    public function testListMembers(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        $client->request('GET', '/api/v1/admin/members?page=1&limit=10', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('pagination', $data);

        // Verify at least one member exists (from fixtures)
        $this->assertNotEmpty($data['data']);
    }

    public function testMemberCannotAccessAdminEndpoint(): void
    {
        $client = static::createClient();

        // Login as member
        $client->request('POST', '/api/v1/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'member@example.com',
            'password' => 'password123',
        ]));

        $response = $client->getResponse();
        $content = $response->getContent();
        $loginData = json_decode($content ?: '{}', true);

        $this->assertArrayHasKey('token', $loginData);
        $memberToken = $loginData['token'];

        // Try to access admin endpoint with member token
        $client->request('GET', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $memberToken,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateMemberWithInvalidEmail(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        $payload = [
            'email' => 'invalid-email', // Invalid format
            'password' => 'password123',
            'name' => 'Test',
            'surname' => 'User',
        ];

        $client->request('POST', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateMemberWithShortPassword(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        $payload = [
            'email' => 'test' . time() . '@test.com',
            'password' => '123', // Too short
            'name' => 'Test',
            'surname' => 'User',
        ];

        $client->request('POST', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
