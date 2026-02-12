<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidUuidException;
use App\Domain\Shared\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    public function testGenerateUuid(): void
    {
        $uuid = Uuid::generate();

        $this->assertInstanceOf(Uuid::class, $uuid);

        // Valid UUID format (any version)
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            (string)$uuid
        );
    }

    public function testGenerateUuidIsUnique(): void
    {
        $uuid1 = Uuid::generate();
        $uuid2 = Uuid::generate();

        $this->assertNotEquals((string)$uuid1, (string)$uuid2);
    }

    public function testFromValidString(): void
    {
        $uuidString = '550e8400-e29b-41d4-a716-446655440000';
        $uuid = Uuid::fromString($uuidString);

        $this->assertEquals($uuidString, (string)$uuid);
    }

    public function testFromValidUuidV7(): void
    {
        // UUID v7 example
        $uuidString = '019c4f7d-9d39-7a0e-a5ee-ecbfb7572ce1';
        $uuid = Uuid::fromString($uuidString);

        $this->assertEquals($uuidString, (string)$uuid);
    }

    public function testInvalidUuidThrowsException(): void
    {
        $this->expectException(InvalidUuidException::class);

        Uuid::fromString('invalid-uuid');
    }

    public function testEmptyUuidThrowsException(): void
    {
        $this->expectException(InvalidUuidException::class);

        Uuid::fromString('');
    }

    public function testUuidEquality(): void
    {
        $uuidString = '550e8400-e29b-41d4-a716-446655440000';
        $uuid1 = Uuid::fromString($uuidString);
        $uuid2 = Uuid::fromString($uuidString);

        $this->assertTrue($uuid1->equals($uuid2));
    }

    public function testUuidInequality(): void
    {
        $uuid1 = Uuid::generate();
        $uuid2 = Uuid::generate();

        $this->assertFalse($uuid1->equals($uuid2));
    }
}
