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

namespace Webify\User\Authorization\Domain\Query\RoleAssignment;

use Webify\User\Authorization\Domain\ValueObject\RoleAssignmentId;

/**
 * Query class defines the contract for a query to find role assignment by its identifier.
 */
interface FindRoleAssignmentByIdInterface
{
	/**
	 * Retrieves a role assignment by its unique identifier.
	 */
	public function find(RoleAssignmentId $id): ?RoleAssignment;
}
