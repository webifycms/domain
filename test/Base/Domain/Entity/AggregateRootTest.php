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

namespace Webify\Test\Base\Domain\Entity;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Entity\AggregateRoot;
use Webify\Base\Domain\Event\DomainEventInterface;

/**
 * AggregateRootTest tests the functionality of the AggregateRoot class.
 *
 * @internal
 */
#[CoversClass(AggregateRoot::class)]
#[CoversMethod(AggregateRoot::class, 'recordDomainEvent')]
#[CoversMethod(AggregateRoot::class, 'getDomainEvents')]
#[CoversMethod(AggregateRoot::class, 'releaseDomainEvents')]
final class AggregateRootTest extends TestCase
{
	/**
	 * Test record domain events.
	 */
	#[Test]
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
	public function testReleaseEventsReturnsEmptyArrayWhenNoEventsRecorded(): void
	{
		$aggregateRoot = new class extends AggregateRoot {};

		$events = $aggregateRoot->releaseDomainEvents();

		$this->assertEmpty($events);
	}
}
