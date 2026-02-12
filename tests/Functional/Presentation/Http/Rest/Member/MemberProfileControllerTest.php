<?php

declare(strict_types=1);

namespace App\Tests\Functional\Presentation\Http\Rest\Member;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class MemberProfileControllerTest extends WebTestCase
{
    private function getAuthToken(KernelBrowser $client): string
    {
        $client->request('POST', '/api/v1/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'member@example.com',
            'password' => 'password123',
        ]));

        $response = $client->getResponse();

        if (!$response->isSuccessful()) {
            $this->fail(sprintf(
                'Member login failed with status %d: %s',
                $response->getStatusCode(),
                $response->getContent()
            ));
        }

        $content = $response->getContent();
        if ($content === false) {
            $this->fail('Member login response is empty');
        }

        $data = json_decode($content, true);

        if (!is_array($data) || !isset($data['token'])) {
            $this->fail('No token in member login response. Response: ' . $content);
        }

        return $data['token'];
    }

    public function testGetProfile(): void
    {
        $client = static::createClient();
        $token = $this->getAuthToken($client);

        $client->request('GET', '/api/v1/profile', [], [], [
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

    public function testUpdateNames(): void
    {
        $client = static::createClient();
        $token = $this->getAuthToken($client);

        $client->request('PATCH', '/api/v1/profile/names', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Updated Name',
        ]));

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('message', $data);
    }

    public function testUpdateNamesWithoutAuthentication(): void
    {
        $client = static::createClient();

        $client->request('PATCH', '/api/v1/profile/names', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Updated Name',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
