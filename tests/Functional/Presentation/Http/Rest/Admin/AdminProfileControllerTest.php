<?php

declare(strict_types=1);

namespace App\Tests\Functional\Presentation\Http\Rest\Admin;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class AdminProfileControllerTest extends WebTestCase
{
    private function getAuthToken(KernelBrowser $client): string
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

    public function testGetProfile(): void
    {
        $client = static::createClient();
        $token = $this->getAuthToken($client);

        $client->request('GET', '/api/v1/admin/profile', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('userId', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('surname', $data);
    }

    public function testUpdateProfile(): void
    {
        $client = static::createClient();
        $token = $this->getAuthToken($client);

        $client->request('PATCH', '/api/v1/admin/profile', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'UpdatedName',
            'surname' => 'UpdatedSurname',
            'phone_number' => '+34612345678',
            'department' => 'Updated Department',
        ]));

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('message', $data);
    }

    public function testUpdateProfileWithoutAuthentication(): void
    {
        $client = static::createClient();

        $client->request('PATCH', '/api/v1/admin/profile', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'UpdatedName',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
