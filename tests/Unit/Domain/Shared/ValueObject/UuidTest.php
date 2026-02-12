<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\DomainException;
use App\Domain\Shared\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    public function testGenerateUuid(): void
    {
        $uuid = Uuid::random();

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertNotEmpty((string)$uuid);
    }

    public function testGenerateUuidIsUnique(): void
    {
        $uuid1 = Uuid::random();
        $uuid2 = Uuid::random();

        $this->assertNotEquals((string)$uuid1, (string)$uuid2);
    }

    public function testFromValidString(): void
    {
        $uuidString = '018c3e9a-6f4c-7c3e-9c3e-6f4c7c3e9c3e';
        $uuid = Uuid::fromString($uuidString);

        $this->assertEquals($uuidString, (string)$uuid);
    }

    public function testFromValidUuidV7(): void
    {
        $uuid = Uuid::create();
        $uuidString = (string)$uuid;

        $reconstructed = Uuid::fromString($uuidString);

        $this->assertEquals($uuidString, (string)$reconstructed);
    }

    public function testInvalidUuidThrowsException(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid UUID format');

        Uuid::fromString('invalid-uuid');
    }

    public function testEmptyUuidThrowsException(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Invalid UUID format');

        Uuid::fromString('');
    }

    public function testUuidEquality(): void
    {
        $uuidString = '018c3e9a-6f4c-7c3e-9c3e-6f4c7c3e9c3e';
        $uuid1 = Uuid::fromString($uuidString);
        $uuid2 = Uuid::fromString($uuidString);

        $this->assertTrue($uuid1->equals($uuid2));
    }

    public function testUuidInequality(): void
    {
        $uuid1 = Uuid::random();
        $uuid2 = Uuid::random();

        $this->assertFalse($uuid1->equals($uuid2));
    }
}
