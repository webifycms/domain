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

namespace Webify\User\Identity\Application\Query\User;

/**
 * Query class defines the contract for a query to find all users.
 */
interface FindAllUserInterface
{
	/**
	 * Retrieves a collection of all users.
	 *
	 * @return UserCollection a collection containing all user entities
	 */
	public function findAll(): UserCollection;
}
