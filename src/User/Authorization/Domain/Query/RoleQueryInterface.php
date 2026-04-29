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

use Webify\User\Authorization\Domain\Collection\RoleReadModelCollection;
use Webify\User\Authorization\Domain\ReadModel\Role;
use Webify\User\Authorization\Domain\ValueObject\{RoleId, RoleSlug};

/**
 * RoleQueryInterface defines the contract for a role assignment query.
 *
 * Defines methods for retrieving roles either by their unique identifier, slug,
 * or as a collection of all available roles.
 */
interface RoleQueryInterface
{
	/**
	 * Retrieves a Role model by its identifier.
	 *
	 * @return null|Role the Role real model if found, or null if no match is found
	 */
	public function findById(RoleId $id): ?Role;

	/**
	 * Retrieves a Role model based on the given slug.
	 *
	 * @return null|Role the Role model if found, or null if no match is found
	 */
	public function findBySlug(RoleSlug $slug): ?Role;

	/**
	 * Retrieves a collection of all Role models.
	 *
	 * @return RoleReadModelCollection the collection containing all Role models
	 */
	public function findAll(): RoleReadModelCollection;
}
