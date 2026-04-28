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

namespace Webify\User\Authorization\Domain\Event;

use DateTimeImmutable;
use Webify\Base\Domain\Event\DomainEventInterface;

/**
 * Event that is triggered when a role was assigned.
 */
final readonly class RoleAssigned implements DomainEventInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		public string $roleAssignmentId,
		public string $roleId,
		public string $subjectId,
		public ?string $tenantId,
		public ?DateTimeImmutable $expiresAt,
		private DateTimeImmutable $createdAt
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
		return 'user.authorization.role_was_assigned';
	}
}
