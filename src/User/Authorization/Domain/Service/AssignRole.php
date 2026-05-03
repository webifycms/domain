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
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authorization\Domain\Entity\{Role, RoleAssignment};
use Webify\User\Authorization\Domain\Guard\DuplicateRoleAssignment;
use Webify\User\Authorization\Domain\Repository\{RoleAssignmentRepositoryInterface, RoleRepositoryInterface};
use Webify\User\Authorization\Domain\ValueObject\{RoleAssignmentId, RoleSlug, SubjectId, TenantId};

/**
 * AssignRole service handles the assigning roles to subjects.
 *
 * Encapsulate the use case of giving a subject a role, optionally scoped to a tenant and with optional expiry.
 * It validates that the role exists before creating the assignment, produces a clear
 * domain exception if it does not, and persists the assignment through the repository.
 */
final readonly class AssignRole
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
	 * Assign the role to the given subject.
	 *
	 * @param string                 $subjectId the ID of the subject to whom the role is assigned
	 * @param string                 $roleSlug  the slug of the role to be assigned
	 * @param null|string            $tenantId  the optional tenant ID for scoping the assignment
	 * @param null|DateTimeImmutable $expireAt  the optional expiration date and time for the assignment
	 */
	public function assign(
		string $subjectId,
		string $roleSlug,
		?string $tenantId = null,
		?DateTimeImmutable $expireAt = null
	): void {
		$subjectId  = SubjectId::fromString($subjectId);
		$role       = $this->getRole($roleSlug);
		$tenantId   = null !== $tenantId ? TenantId::fromString($tenantId) : null;

		// Guard against duplicate role assignments
		$this->duplicateRoleAssignment->guard($role->getId(), $subjectId, $tenantId);

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
