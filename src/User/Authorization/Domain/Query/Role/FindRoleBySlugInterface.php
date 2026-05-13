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

use Webify\User\Authorization\Domain\ValueObject\RoleSlug;

/**
 * Query class defines the contract for a query to find a role by its slug.
 */
interface FindRoleBySlugInterface
{
	/**
	 * Retrieves a Role model based on the given slug.
	 *
	 * @return null|Role the Role model if found, or null if no match is found
	 */
	public function find(RoleSlug $slug): ?Role;
}
