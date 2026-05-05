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

namespace Webify\Test\Base\Domain\Entity\Fixture;

use Webify\Base\Domain\Entity\AggregateRoot;
use Webify\Base\Domain\Event\DomainEventInterface;

/**
 * SampleAggregateRoot is a sample aggregate root for testing purposes.
 */
final class TestableAggregateRoot extends AggregateRoot
{
	/**
	 * Records a domain event to the event stream for later handling or persistence.
	 *
	 * @param DomainEventInterface $event the domain event to be recorded
	 */
	public function recordDomainEvent(DomainEventInterface $event): void
	{
		parent::recordDomainEvent($event);
	}
}
