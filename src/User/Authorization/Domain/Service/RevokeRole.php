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
use Webify\Base\Domain\Service\Authorization\RevokeRoleInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authorization\Domain\Event\RoleRevoked;
use Webify\User\Authorization\Domain\Exception\RoleAssignmentNotFoundException;
use Webify\User\Authorization\Domain\Repository\RoleAssignmentRepositoryInterface;
use Webify\User\Authorization\Domain\ValueObject\RoleAssignmentId;

/**
 * RevokeRole service class is a concrete implementation of the RevokeRoleInterface.
 */
final readonly class RevokeRole implements RevokeRoleInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private RoleAssignmentRepositoryInterface $roleAssignmentRepository,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * {@inheritDoc}
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
