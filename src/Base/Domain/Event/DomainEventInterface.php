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

use DateTimeImmutable;

/**
 * DomainEventInterface defines the contract for domain events in the system.
 */
interface DomainEventInterface
{
	/**
	 * Get the date and time when the event occurred.
	 */
	public function occurredOn(): DateTimeImmutable;

	/**
	 * Get the name of the event.
	 */
	public function eventName(): string;
}
