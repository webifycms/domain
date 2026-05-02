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

use Webify\User\Authorization\Domain\Entity\Role;
use Webify\User\Authorization\Domain\Exception\RoleNotFoundException;
use Webify\User\Authorization\Domain\ValueObject\{RoleId, RoleSlug};

/**
 * RoleRepositoryInterface defines the contract for a role repository.
 */
interface RoleRepositoryInterface
{
	/**
	 * Retrieves a Role entity by its ID.
	 *
	 * @throws RoleNotFoundException if the role with the given ID is not found
	 */
	public function getById(RoleId $id): Role;

	/**
	 * Retrieves a Role entity by its slug.
	 *
	 * @throws RoleNotFoundException if the role with the given slug is not found
	 */
	public function getBySlug(RoleSlug $slug): Role;

	/**
	 * Persists the given Role entity to the data store.
	 */
	public function persist(Role $role): void;
}
