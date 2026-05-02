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

namespace Webify\User\Authorization\Domain\Repository;

use Webify\User\Authorization\Domain\Entity\RoleAssignment;
use Webify\User\Authorization\Domain\Exception\RoleAssignmentNotFoundException;
use Webify\User\Authorization\Domain\ValueObject\{RoleAssignmentId, RoleId, SubjectId, TenantId};

/**
 * RoleAssignmentRepositoryInterface defines the contract for a role assignment repository.
 */
interface RoleAssignmentRepositoryInterface
{
	/**
	 * Retrieves a RoleAssignment object by its unique identifier.
	 *
	 * @param RoleAssignmentId $id the unique identifier of the RoleAssignment
	 *
	 * @return RoleAssignment the RoleAssignment object associated with the given identifier
	 *
	 * @throws RoleAssignmentNotFoundException if the RoleAssignment with the given identifier is not found
	 */
	public function getById(RoleAssignmentId $id): RoleAssignment;

	/**
	 * Checks if a role assignment exists for the given role ID and subject ID.
	 *
	 * @param RoleId        $roleId    the ID of the role
	 * @param SubjectId     $subjectId the ID of the subject
	 * @param null|TenantId $tenantId  the ID of the tenant, if applicable
	 *
	 * @return bool true if a role assignment exists, false otherwise
	 */
	public function isExists(RoleId $roleId, SubjectId $subjectId, ?TenantId $tenantId): bool;

	/**
	 * Persists the given RoleAssignment object to the data store.
	 *
	 * @param RoleAssignment $roleAssignment the RoleAssignment object to be saved
	 */
	public function persist(RoleAssignment $roleAssignment): void;

	/**
	 * Deletes the specified role assignment.
	 *
	 * @param RoleAssignment $roleAssignment the role assignment instance to be deleted
	 */
	public function delete(RoleAssignment $roleAssignment): void;
}
