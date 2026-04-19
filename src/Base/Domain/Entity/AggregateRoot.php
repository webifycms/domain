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

namespace Webify\Base\Domain\Entity;

use Webify\Base\Domain\Event\DomainEventInterface;

/**
 * The aggregate root base class.
 */
abstract class AggregateRoot
{
	/**
	 * The recorded domain events list.
	 *
	 * @var DomainEventInterface[]
	 */
	private array $domainEvents = [];

	/**
	 * Record an event.
	 */
	public function recordDomainEvent(DomainEventInterface $event): void
	{
		$this->domainEvents[] = $event;
	}

	/**
	 * Get domain events.
	 *
	 * @return DomainEventInterface[]
	 */
	public function getDomainEvents(): array
	{
		return $this->domainEvents;
	}

	/**
	 * Clear all the domain events.
	 *
	 * @return DomainEventInterface[]
	 */
	public function releaseDomainEvents(): array
	{
		$events               = $this->domainEvents;
		$this->domainEvents   = [];

		return $events;
	}
}
