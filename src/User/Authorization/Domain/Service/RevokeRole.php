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

namespace Webify\User\Authorization\Domain\Service;

use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authorization\Domain\Event\RoleRevoked;
use Webify\User\Authorization\Domain\Exception\RoleAssignmentNotFoundException;
use Webify\User\Authorization\Domain\Repository\RoleAssignmentRepositoryInterface;
use Webify\User\Authorization\Domain\ValueObject\RoleAssignmentId;

/**
 * RevokeRole service for revoking roles from subjects.
 *
 * Encapsulate the use case of removing a role assignment from a subject.
 * Revocation must be scoped to the same tenant context the assignment was created in — revoking globally
 * should not affect tenant-scoped assignments and vice versa.
 *
 * The service is silent if the assignment does not exist (idempotent behaviour), so callers do not need to check first.
 */
final readonly class RevokeRole
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private RoleAssignmentRepositoryInterface $roleAssignmentRepository,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Revoke the role from the given subject.
	 *
	 * @param string $roleAssignmentId the ID of the role assignment to be revoked
	 */
	public function revoke(string $roleAssignmentId): void
	{
		$id = RoleAssignmentId::fromString($roleAssignmentId);

		try {
			$assignment = $this->roleAssignmentRepository->getById($id);

			$this->roleAssignmentRepository->delete($assignment);

			$this->eventPublisher->publish(
				new RoleRevoked(
					$id->toNative(),
					$assignment->getRoleId()->toNative(),
					$assignment->getSubjectId()->toNative(),
					$assignment->getTenantId()?->toNative(),
					$assignment->getExpiresAt()?->toNative(),
					DateTime::now()->toNative()
				)
			);
		} catch (RoleAssignmentNotFoundException) {
			// Silent if the assignment does not exist (idempotent behaviour)
			return;
		}
	}
}
