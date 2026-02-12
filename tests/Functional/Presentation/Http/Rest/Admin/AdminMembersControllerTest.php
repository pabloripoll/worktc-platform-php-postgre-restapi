<?php

declare(strict_types=1);

namespace App\Tests\Functional\Presentation\Http\Rest\Admin;

use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class AdminMembersControllerTest extends WebTestCase
{
    /**
     * Login as admin and return JWT token
     *
     * @param KernelBrowser $client The test client
     * @return string The JWT authentication token
     */
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
        if ($content === false) {
            $this->fail('Admin login response is empty');
        }

        $data = json_decode($content, true);

        if (!is_array($data) || !isset($data['token'])) {
            $this->fail('No token in admin login response. Response: ' . $content);
        }

        return $data['token'];
    }

    /**
     * Test creating a member with all optional fields
     *
     * @return void
     */
    public function testCreateMemberWithAllFields(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        $client->request('POST', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'newmember_' . time() . '@example.com',
            'password' => 'SecurePass123!',
            'name' => 'John',
            'surname' => 'Doe',
            'phone_number' => '+34612345678',
            'department' => 'Engineering',
            'birth_date' => '1990-01-15',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('message', $data);
    }

    /**
     * Test creating a member with only required fields
     *
     * @return void
     */
    public function testCreateMemberWithMinimalFields(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        $client->request('POST', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'minimal_' . time() . '@example.com',
            'password' => 'SecurePass123!',
            'name' => 'Jane',
            'surname' => 'Smith',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('user_id', $data);
    }

    /**
     * Test listing members with pagination
     *
     * @return void
     */
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
    }

    /**
     * Test updating a member's profile information
     *
     * This test disables DAMA transaction wrapping to ensure real commits
     *
     * @return void
     */
    public function testUpdateMemberProfile(): void
    {
        // Disable DAMA for this test to ensure real database commits
        StaticDriver::setKeepStaticConnections(false);

        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        // Create a member within this test
        $client->request('POST', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'toupdate_' . time() . '@example.com',
            'password' => 'SecurePass123!',
            'name' => 'OriginalName',
            'surname' => 'OriginalSurname',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $createContent = $client->getResponse()->getContent();
        $this->assertNotFalse($createContent);

        $createData = json_decode($createContent, true);
        $this->assertArrayHasKey('user_id', $createData);

        $memberId = $createData['user_id'];

        // Update the member
        $client->request('PATCH', "/api/v1/admin/members/{$memberId}/profiles", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'UpdatedName',
            'surname' => 'UpdatedSurname',
            'phone_number' => '+34999888777',
        ]));

        $this->assertResponseIsSuccessful();

        // Shutdown and restart to get fresh EntityManager
        self::ensureKernelShutdown();
        $client = static::createClient();

        // Verify the update
        $client->request('GET', "/api/v1/admin/members/{$memberId}/profiles", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $profileContent = $client->getResponse()->getContent();
        $this->assertNotFalse($profileContent);

        $profileData = json_decode($profileContent, true);
        $this->assertIsArray($profileData);

        $this->assertEquals('UpdatedName', $profileData['name']);
        $this->assertEquals('UpdatedSurname', $profileData['surname']);
        $this->assertEquals('+34999888777', $profileData['phone_number']);

        // Cleanup - delete the test user
        $client->request('DELETE', "/api/v1/admin/members/{$memberId}/profiles", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        // Re-enable DAMA for subsequent tests
        StaticDriver::setKeepStaticConnections(true);
    }

    /**
     * Test soft-deleting a member profile
     *
     * @return void
     */
    public function testDeleteMemberProfile(): void
    {
        $client = static::createClient();
        $token = $this->loginAsAdmin($client);

        // Create a member to delete
        $client->request('POST', '/api/v1/admin/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'todelete_' . time() . '@example.com',
            'password' => 'SecurePass123!',
            'name' => 'Delete',
            'surname' => 'Me',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertArrayHasKey('user_id', $data);

        $memberId = $data['user_id'];

        // Delete the member
        $client->request('DELETE', "/api/v1/admin/members/{$memberId}/profiles", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();

        $deleteContent = $client->getResponse()->getContent();
        $this->assertNotFalse($deleteContent);

        $deleteData = json_decode($deleteContent, true);
        $this->assertArrayHasKey('message', $deleteData);
    }
}
