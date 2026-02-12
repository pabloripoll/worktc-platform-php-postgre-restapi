<?php

declare(strict_types=1);

namespace App\Tests\Functional\Presentation\Http\Rest\Member;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class MemberAuthControllerTest extends WebTestCase
{
    public function testMemberLoginSuccess(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'member@example.com',
            'password' => 'password123',
        ]));

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $content = $response->getContent();
        $this->assertNotFalse($content);

        $data = json_decode($content, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('token', $data);
        $this->assertArrayHasKey('expires_in', $data);
        $this->assertNotEmpty($data['token']);
        $this->assertEquals(3600, $data['expires_in']);
    }

    public function testMemberLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'member@example.com',
            'password' => 'wrong_password',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testMemberLoginWithNonExistentUser(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
