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

namespace Webify\Base\Domain\Event;

/**
 * DomainEventPublisherInterface defines the contract for publishing domain events.
 */
interface DomainEventPublisherInterface
{
	/**
	 * Publish one or more domain events.
	 *
	 * @param DomainEventInterface ...$events The domain events to publish.
	 */
	public function publish(DomainEventInterface ...$events): void;
}
