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

namespace Webify\User\Authorization\Domain\Entity;

use Webify\Base\Domain\Entity\AggregateRoot;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authorization\Domain\Event\RoleAssigned;
use Webify\User\Authorization\Domain\ValueObject\{RoleAssignmentId, RoleId, SubjectId, TenantId};

/**
 * Role assignment entity.
 *
 * Represent the link between a subject and a role, optionally scoped to a tenant.
 * This is what makes the system multi-tenant-aware at the data level — the same role
 * can be assigned globally (tenantId = null) or restricted to a specific organization.
 * An expiry date is supported so temporary elevated access (e.g. covering a colleague's leave)
 * can be modelled without needing a separate revocation step.
 * The isApplicableFor method encapsulates the scoping logic, so callers never perform tenant comparisons manually.
 */
final class RoleAssignment extends AggregateRoot
{
	/**
	 * Private constructor enforces the use of the factory method.
	 *
	 * @param RoleAssignmentId $id        unique identifier for the role assignment
	 * @param RoleId           $roleId    identifier of the associated role
	 * @param SubjectId        $subjectId identifier of the subject tied to the role assignment
	 * @param null|TenantId    $tenantId  optional identifier for the tenant
	 * @param null|DateTime    $expiresAt optional expiration date for the role assignment, if null it will never expire
	 */
	private function __construct(
		private readonly RoleAssignmentId $id,
		private readonly RoleId $roleId,
		private readonly SubjectId $subjectId,
		private readonly ?TenantId $tenantId= null,
		private readonly ?DateTime $expiresAt = null
	) {}

	/**
	 * Get the identifier.
	 */
	public function getId(): RoleAssignmentId
	{
		return $this->id;
	}

	/**
	 * Get the role identifier.
	 */
	public function getRoleId(): RoleId
	{
		return $this->roleId;
	}

	/**
	 * Get the subject identifier.
	 */
	public function getSubjectId(): SubjectId
	{
		return $this->subjectId;
	}

	/**
	 * Get the tenant identifier.
	 */
	public function getTenantId(): ?TenantId
	{
		return $this->tenantId;
	}

	/**
	 * Get the expiration date of the role assignment.
	 */
	public function getExpiresAt(): ?DateTime
	{
		return $this->expiresAt;
	}

	/**
	 * Check if the role assignment is applicable for the given tenant.
	 */
	public function isApplicableFor(?TenantId $tenantId): bool
	{
		// This means global role assignment
		if (null === $this->tenantId) {
			return true;
		}

		// This means role assignment is scope, but context is global
		if (null === $tenantId) {
			return false;
		}

		return $this->tenantId->equals($tenantId);
	}

	/**
	 * Check if the role assignment has expired.
	 */
	public function isExpired(): bool
	{
		if (null === $this->expiresAt) {
			return false;
		}

		return $this->expiresAt->isAfter(DateTime::now());
	}

	/**
	 * Factory method to assign a new role.
	 */
	public static function assign(
		RoleAssignmentId $id,
		RoleId $roleId,
		SubjectId $subjectId,
		?TenantId $tenantId = null,
		?DateTime $expiresAt = null
	): self {
		$roleAssignment = new self($id, $roleId, $subjectId, $tenantId, $expiresAt);

		$roleAssignment->recordDomainEvent(
			new RoleAssigned(
				$roleAssignment->getId()->toNative(),
				$roleAssignment->getRoleId()->toNative(),
				$roleAssignment->getSubjectId()->toNative(),
				$roleAssignment->getTenantId()?->toNative(),
				$roleAssignment->getExpiresAt()?->toNative(),
				DateTime::now()->toNative()
			)
		);

		return $roleAssignment;
	}
}
