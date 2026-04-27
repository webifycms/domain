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
 * Event that is triggered when a role was created.
 */
final readonly class RoleCreated implements DomainEventInterface
{
	/**
	 * The constructor.
	 *
	 * @param string            $roleId       the role ID
	 * @param string            $roleName     the role name
	 * @param string            $roleSlug     the role slug
	 * @param array<string>     $permissions  the permissions associated with the role
	 * @param bool              $isSystemRole whether the role is a system role
	 * @param DateTimeImmutable $createdAt    the date and time when the role was created
	 */
	public function __construct(
		public string $roleId,
		public string $roleName,
		public string $roleSlug,
		public array $permissions,
		public bool $isSystemRole,
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
		return 'user.authorization.role_was_created';
	}
}
