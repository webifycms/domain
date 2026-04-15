<?php

/**
 * The file is part of the "webifycms/domain", WebifyCMS extension package.
 *
 * @see https://webifycms.com/extension/domain
 *
 * @copyright Copyright (c) 2023 WebifyCMS
 * @license https://webifycms.com/extension/domain/license
 * @author Mohammed Shifreen <mshifreen@gmail.com>
 */
declare(strict_types=1);

namespace Webifycms\Domain\Test\Base\Domain\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Webifycms\Domain\Base\Domain\Entity\AggregateRoot;
use Webifycms\Domain\Base\Domain\Event\DomainEventInterface;

/**
 * AggregateRootTest tests the functionality of the AggregateRoot class.
 *
 * @internal
 */
#[CoversClass(AggregateRoot::class)]
final class AggregateRootTest extends TestCase
{
	/**
	 * Test record domain events.
	 */
	#[Test]
	#[CoversMethod(AggregateRoot::class, 'recordDomainEvent')]
	#[CoversMethod(AggregateRoot::class, 'getDomainEvents')]
	public function testRecordEvents(): void
	{
		$aggregateRoot = new class extends AggregateRoot {};

		$event1 = $this->createMock(DomainEventInterface::class);
		$event2 = $this->createMock(DomainEventInterface::class);

		$aggregateRoot->recordDomainEvent($event1);
		$aggregateRoot->recordDomainEvent($event2);

		$events = $aggregateRoot->getDomainEvents();

		$this->assertCount(2, $events);
		$this->assertSame($event1, $events[0]);
		$this->assertSame($event2, $events[1]);
	}

	/**
	 * Test clear domain events.
	 */
	#[Test]
	#[CoversMethod(AggregateRoot::class, 'getDomainEvents')]
	#[CoversMethod(AggregateRoot::class, 'releaseDomainEvents')]
	public function testReleaseEvents(): void
	{
		$aggregateRoot = new class extends AggregateRoot {};

		$event1 = $this->createMock(DomainEventInterface::class);
		$event2 = $this->createMock(DomainEventInterface::class);

		$aggregateRoot->recordDomainEvent($event1);
		$aggregateRoot->recordDomainEvent($event2);

		$events = $aggregateRoot->releaseDomainEvents();

		$this->assertCount(2, $events);
		$this->assertSame($event1, $events[0]);
		$this->assertSame($event2, $events[1]);

		// Ensure events are cleared after release
		$this->assertEmpty($aggregateRoot->getDomainEvents());
	}

	/**
	 * Test release events returns empty when no events were recorded.
	 */
	#[Test]
	#[CoversMethod(AggregateRoot::class, 'releaseDomainEvents')]
	public function testReleaseEventsReturnsEmptyArrayWhenNoEventsRecorded(): void
	{
		$aggregateRoot = new class extends AggregateRoot {};

		$events = $aggregateRoot->releaseDomainEvents();

		$this->assertEmpty($events);
	}
}
