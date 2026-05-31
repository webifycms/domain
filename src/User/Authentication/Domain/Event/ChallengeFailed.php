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

namespace Webify\User\Authentication\Domain\Event;

use DateTimeImmutable;
use Webify\Base\Domain\Event\DomainEventInterface;

/**
 * The event that is triggered when an authentication challenge failed.
 */
final readonly class ChallengeFailed implements DomainEventInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		public string $challengeId,
		public string $userId,
		public string $type,
		public int $attempts,
		public DateTimeImmutable $createdAt
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function occurredOn(): DateTimeImmutable
	{
		return $this->createdAt;
	}

	/**
	 * {@inheritDoc}
	 */
	public function eventName(): string
	{
		return 'user.authentication.challenge_failed';
	}
}
