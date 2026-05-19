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

namespace Webify\User\Authorization\Application\Query\Role;

/**
 * Query class defines the contract for a query to find all roles.
 */
interface FindAllRolesInterface
{
	/**
	 * Retrieves a collection of all Role models.
	 *
	 * @return RoleCollection the collection containing all Role models
	 */
	public function findAll(): RoleCollection;
}
