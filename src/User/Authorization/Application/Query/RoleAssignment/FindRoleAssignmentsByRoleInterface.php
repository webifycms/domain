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

use Webify\User\Authorization\Domain\ValueObject\RoleId;

/**
 * Query class defines the contract for a query to find role assignments by role.
 */
interface FindRoleAssignmentsByRoleInterface
{
	/**
	 * Retrieves a collection of role assignments associated with the specified role ID.
	 *
	 * @param RoleId $roleId the identifier of the role to find assignments for
	 *
	 * @return RoleAssignmentCollection a collection of role assignments corresponding to the role ID
	 */
	public function find(RoleId $roleId): RoleAssignmentCollection;
}
