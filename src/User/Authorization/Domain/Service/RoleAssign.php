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

use DateTimeImmutable;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\{RoleAssignInterface, UlidGeneratorInterface};
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authorization\Domain\Entity\{Role, RoleAssignment};
use Webify\User\Authorization\Domain\Guard\DuplicateRoleAssignment;
use Webify\User\Authorization\Domain\Repository\{RoleAssignmentRepositoryInterface, RoleRepositoryInterface};
use Webify\User\Authorization\Domain\ValueObject\{RoleAssignmentId, RoleSlug, SubjectId, TenantId};

/**
 * RoleAssign service is implements of RoleAssignInterface.
 */
final readonly class RoleAssign implements RoleAssignInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private RoleRepositoryInterface $roleRepository,
		private RoleAssignmentRepositoryInterface $roleAssignmentRepository,
		private UlidGeneratorInterface $idGenerator,
		private DuplicateRoleAssignment $duplicateRoleAssignment,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function assign(
		string $subjectId,
		string $roleSlug,
		?string $tenantId = null,
		?DateTimeImmutable $expireAt = null
	): void {
		$subjectId  = SubjectId::fromString($subjectId);
		$role       = $this->getRole($roleSlug);

		// Guard against duplicate role assignments
		$this->duplicateRoleAssignment->guard($role->getId(), $subjectId);

		$tenantId   = null !== $tenantId ? TenantId::fromString($tenantId) : null;
		$expireAt   = null !== $expireAt ? DateTime::fromNative($expireAt) : null;
		$assignment = RoleAssignment::assign(
			RoleAssignmentId::fromString($this->idGenerator->generate()),
			$role->getId(),
			$subjectId,
			$tenantId,
			$expireAt
		);

		$this->roleAssignmentRepository->persist($assignment);
		$this->eventPublisher->publish(...$assignment->getDomainEvents());
	}

	/**
	 * Retrieves a Role object based on the provided role slug.
	 *
	 * @param string $roleSlug the slug of the role to retrieve
	 *
	 * @return Role the Role object corresponding to the given slug
	 */
	private function getRole(string $roleSlug): Role
	{
		return $this->roleRepository->getBySlug(RoleSlug::fromString($roleSlug));
	}
}
