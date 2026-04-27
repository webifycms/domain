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
 * Event that is triggered when permission revoked.
 */
final readonly class PermissionRevoked implements DomainEventInterface
{
	/**
	 * The constructor.
	 *
	 * @param string            $roleId     the role ID
	 * @param string            $roleName   the role name
	 * @param string            $roleSlug   the role slug
	 * @param string            $permission the revoked permission
	 * @param DateTimeImmutable $createdAt  the date and time when the role was created
	 */
	public function __construct(
		public string $roleId,
		public string $roleName,
		public string $roleSlug,
		public string $permission,
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
		return 'user.authorization.permission_was_revoked';
	}
}
