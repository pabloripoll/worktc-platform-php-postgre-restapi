<?php

declare(strict_types=1);

namespace App\Tests\Functional\Presentation\Http\Rest\Member;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class MemberClockingControllerTest extends WebTestCase
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

    public function testCreateClocking(): void
    {
        $client = static::createClient();
        $token = $this->getAuthToken($client);

        $client->request('POST', '/api/v1/clockings', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'start_date' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
    }

    public function testListClockings(): void
    {
        $client = static::createClient();
        $token = $this->getAuthToken($client);

        $client->request('GET', '/api/v1/clockings', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
    }

    public function testGetSpecificClocking(): void
    {
        $client = static::createClient();
        $token = $this->getAuthToken($client);

        // First create a clocking
        $client->request('POST', '/api/v1/clockings', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'start_date' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ]));

        $createContent = $client->getResponse()->getContent();
        $this->assertNotFalse($createContent);

        $createData = json_decode($createContent, true);
        $this->assertIsArray($createData);
        $this->assertArrayHasKey('id', $createData);

        $clockingId = $createData['id'];

        // Then retrieve it
        $client->request('GET', '/api/v1/clockings/' . $clockingId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertEquals($clockingId, $data['id']);
    }

    public function testUpdateClocking(): void
    {
        $client = static::createClient();
        $token = $this->getAuthToken($client);

        // Create a clocking
        $client->request('POST', '/api/v1/clockings', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'start_date' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ]));

        $createContent = $client->getResponse()->getContent();
        $this->assertNotFalse($createContent);

        $createData = json_decode($createContent, true);
        $this->assertIsArray($createData);
        $this->assertArrayHasKey('id', $createData);

        $clockingId = $createData['id'];

        // Update it
        $client->request('PATCH', '/api/v1/clockings/' . $clockingId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'end_date' => (new \DateTimeImmutable('+8 hours'))->format(\DateTimeInterface::ATOM),
        ]));

        $this->assertResponseIsSuccessful();
    }

    public function testDeleteClocking(): void
    {
        $client = static::createClient();
        $token = $this->getAuthToken($client);

        // Create a clocking
        $client->request('POST', '/api/v1/clockings', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'start_date' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ]));

        $createContent = $client->getResponse()->getContent();
        $this->assertNotFalse($createContent);

        $createData = json_decode($createContent, true);
        $this->assertIsArray($createData);
        $this->assertArrayHasKey('id', $createData);

        $clockingId = $createData['id'];

        // Delete it
        $client->request('DELETE', '/api/v1/clockings/' . $clockingId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);

        $this->assertResponseIsSuccessful();
    }
}
