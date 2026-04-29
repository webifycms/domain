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

namespace Webify\User\Authorization\Domain\Query;

use Webify\User\Authorization\Domain\Collection\RoleAssignmentReadModelCollection;
use Webify\User\Authorization\Domain\ReadModel\RoleAssignment;
use Webify\User\Authorization\Domain\ValueObject\{RoleAssignmentId, RoleId, SubjectId, TenantId};

/**
 * RoleAssignmentQueryInterface defines the contract for a role assignment query.
 *
 * Defines methods for retrieving role assignments by their unique identifier, role ID, subject ID, or tenant ID.
 * And a method to retrieve all role assignments models as a collection.
 */
interface RoleAssignmentQueryInterface
{
	/**
	 * Retrieves a role assignment by its unique identifier.
	 */
	public function findById(RoleAssignmentId $id): ?RoleAssignment;

	/**
	 * Retrieves a collection of role assignments associated with the specified role ID.
	 *
	 * @param RoleId $roleId the identifier of the role to find assignments for
	 *
	 * @return RoleAssignmentReadModelCollection a collection of role assignments corresponding to the role ID
	 */
	public function findByRoleId(RoleId $roleId): RoleAssignmentReadModelCollection;

	/**
	 * Retrieves a collection of role assignments associated with a specific subject identifier.
	 *
	 * @param SubjectId $subjectId the unique identifier of the subject
	 *
	 * @return RoleAssignmentReadModelCollection a collection of role assignment read models linked
	 *                                           to the specified subject
	 */
	public function findBySubjectId(SubjectId $subjectId): RoleAssignmentReadModelCollection;

	/**
	 * Retrieves a collection of role assignment read models associated with a specific tenant.
	 *
	 * @param TenantId $tenantId the unique identifier of the tenant
	 *
	 * @return RoleAssignmentReadModelCollection the collection of role assignment read models for the given tenant
	 */
	public function findByTenantId(TenantId $tenantId): RoleAssignmentReadModelCollection;

	/**
	 * Retrieves all role assignments.
	 *
	 * @return RoleAssignmentReadModelCollection a collection containing all role assignments
	 */
	public function findAll(): RoleAssignmentReadModelCollection;
}
