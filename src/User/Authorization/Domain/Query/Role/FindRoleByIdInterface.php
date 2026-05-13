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

namespace Webify\User\Authorization\Domain\Query\Role;

use Webify\User\Authorization\Domain\ValueObject\RoleId;

/**
 * Query class defines the contract for a query to find a role by its identifier.
 */
interface FindRoleByIdInterface
{
	/**
	 * Retrieves a Role model by its identifier.
	 *
	 * @return null|Role the Role real model if found, or null if no match is found
	 */
	public function find(RoleId $id): ?Role;
}
