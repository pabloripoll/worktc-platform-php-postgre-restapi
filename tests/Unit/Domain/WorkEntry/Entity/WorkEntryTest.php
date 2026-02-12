<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\WorkEntry\Entity;

use App\Domain\Shared\ValueObject\Uuid;
use App\Domain\WorkEntry\Entity\WorkEntry;
use PHPUnit\Framework\TestCase;

final class WorkEntryTest extends TestCase
{
    public function testStartWorkEntry(): void
    {
        $id = Uuid::generate();
        $userId = Uuid::generate();
        $createdByUserId = Uuid::generate();
        $startDate = new \DateTimeImmutable('2025-01-15 08:00:00');

        $workEntry = WorkEntry::start($id, $userId, $startDate, $createdByUserId);

        $this->assertEquals($id, $workEntry->getId());
        $this->assertEquals($userId, $workEntry->getUserId());
        $this->assertEquals($startDate, $workEntry->getStartDate());
        $this->assertNull($workEntry->getEndDate());
        $this->assertEquals($createdByUserId, $workEntry->getCreatedByUserId());
        $this->assertFalse($workEntry->isDeleted());
        $this->assertTrue($workEntry->isActive());
        $this->assertInstanceOf(\DateTimeImmutable::class, $workEntry->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $workEntry->getUpdatedAt());
    }

    public function testStartWorkEntryWithoutCreator(): void
    {
        $id = Uuid::generate();
        $userId = Uuid::generate();
        $startDate = new \DateTimeImmutable('2025-01-15 08:00:00');

        $workEntry = WorkEntry::start($id, $userId, $startDate);

        $this->assertEquals($id, $workEntry->getId());
        $this->assertEquals($userId, $workEntry->getUserId());
        $this->assertNull($workEntry->getCreatedByUserId());
    }

    public function testEndWorkEntry(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $this->assertTrue($workEntry->isActive());

        $endDate = new \DateTimeImmutable('2025-01-15 17:00:00');
        $updatedByUserId = Uuid::generate();

        $workEntry->end($endDate, $updatedByUserId);

        $this->assertEquals($endDate, $workEntry->getEndDate());
        $this->assertEquals($updatedByUserId, $workEntry->getUpdatedByUserId());
        $this->assertFalse($workEntry->isActive());
    }

    public function testEndWorkEntryWithoutUpdater(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $endDate = new \DateTimeImmutable('2025-01-15 17:00:00');
        $workEntry->end($endDate);

        $this->assertEquals($endDate, $workEntry->getEndDate());
        $this->assertNull($workEntry->getUpdatedByUserId());
    }

    public function testEndDateCannotBeBeforeStartDate(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('End date cannot be before start date');

        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 17:00:00')
        );

        $workEntry->end(new \DateTimeImmutable('2025-01-15 08:00:00'));
    }

    public function testCannotEndAlreadyEndedWorkEntry(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Work entry already ended');

        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $workEntry->end(new \DateTimeImmutable('2025-01-15 17:00:00'));

        // Try to end again
        $workEntry->end(new \DateTimeImmutable('2025-01-15 18:00:00'));
    }

    public function testCalculateDuration(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $workEntry->end(new \DateTimeImmutable('2025-01-15 17:00:00'));

        $duration = $workEntry->getDuration();

        $this->assertInstanceOf(\DateInterval::class, $duration);
        $this->assertEquals(9, $duration->h);
        $this->assertEquals(0, $duration->i);
    }

    public function testCalculateDurationInMinutes(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $workEntry->end(new \DateTimeImmutable('2025-01-15 17:00:00'));

        // 9 hours = 540 minutes
        $this->assertEquals(540, $workEntry->getDurationInMinutes());
    }

    public function testDurationIsNullWhenNotEnded(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $this->assertNull($workEntry->getDuration());
        $this->assertNull($workEntry->getDurationInMinutes());
    }

    public function testWorkEntryIsActive(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $this->assertTrue($workEntry->isActive());
    }

    public function testWorkEntryIsNotActiveWhenEnded(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $workEntry->end(new \DateTimeImmutable('2025-01-15 17:00:00'));

        $this->assertFalse($workEntry->isActive());
    }

    public function testWorkEntryIsNotActiveWhenDeleted(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $workEntry->softDelete();

        $this->assertFalse($workEntry->isActive());
    }

    public function testSoftDelete(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable()
        );

        $this->assertFalse($workEntry->isDeleted());
        $this->assertNull($workEntry->getDeletedAt());

        $deletedByUserId = Uuid::generate();
        $workEntry->softDelete($deletedByUserId);

        $this->assertTrue($workEntry->isDeleted());
        $this->assertInstanceOf(\DateTimeImmutable::class, $workEntry->getDeletedAt());
        $this->assertEquals($deletedByUserId, $workEntry->getUpdatedByUserId());
    }

    public function testSoftDeleteWithoutDeleter(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable()
        );

        $workEntry->softDelete();

        $this->assertTrue($workEntry->isDeleted());
        $this->assertNull($workEntry->getUpdatedByUserId());
    }

    public function testUpdatedAtChangesOnEnd(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 08:00:00')
        );

        $originalUpdatedAt = $workEntry->getUpdatedAt();

        usleep(1000); // Ensure time difference

        $workEntry->end(new \DateTimeImmutable('2025-01-15 17:00:00'));

        $this->assertGreaterThan($originalUpdatedAt, $workEntry->getUpdatedAt());
    }

    public function testUpdatedAtChangesOnSoftDelete(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable()
        );

        $originalUpdatedAt = $workEntry->getUpdatedAt();

        usleep(1000); // Ensure time difference

        $workEntry->softDelete();

        $this->assertGreaterThan($originalUpdatedAt, $workEntry->getUpdatedAt());
    }

    public function testDurationAcrossMultipleDays(): void
    {
        $workEntry = WorkEntry::start(
            Uuid::generate(),
            Uuid::generate(),
            new \DateTimeImmutable('2025-01-15 22:00:00')
        );

        $workEntry->end(new \DateTimeImmutable('2025-01-16 06:00:00'));

        // 8 hours across 2 days = 480 minutes
        $this->assertEquals(480, $workEntry->getDurationInMinutes());
    }
}
