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

namespace Webify\User\Authorization\Application\Query\RoleAssignment;

/**
 * RoleAssignmentQueryInterface defines the contract for a role assignment query.
 *
 * Defines methods for retrieving role assignments by their unique identifier, role ID, subject ID, or tenant ID.
 * And a method to retrieve all role assignments models as a collection.
 */
interface FindAllRoleAssignmentsInterface
{
	/**
	 * Retrieves all role assignments.
	 *
	 * @return RoleAssignmentCollection a collection containing all role assignments
	 */
	public function findAll(): RoleAssignmentCollection;
}
