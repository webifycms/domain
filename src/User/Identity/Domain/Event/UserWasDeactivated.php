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

namespace Webify\User\Identity\Domain\Event;

use DateTimeImmutable;
use Webify\Base\Domain\Event\DomainEventInterface;
use Webify\Base\Domain\ValueObject\DateTime;

/**
 * The event that is triggered when a user was deactivated.
 */
final readonly class UserWasDeactivated implements DomainEventInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		public string $userId,
		public string $email,
		private DateTime $createdAt
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function occurredOn(): DateTimeImmutable
	{
		return $this->createdAt->toNative();
	}

	/**
	 * {@inheritDoc}
	 */
	public function eventName(): string
	{
		return 'user.identity.deactivated';
	}
}
