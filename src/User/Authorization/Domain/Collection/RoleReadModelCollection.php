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

namespace Webify\User\Authorization\Domain\Collection;

use Webify\Base\Domain\Collection\Collection;
use Webify\User\Authorization\Domain\ReadModel\Role;

/**
 * Collection class for Role-read models.
 *
 * @extends Collection<Role>
 */
final class RoleReadModelCollection extends Collection
{
	/**
	 * {@inheritDoc}
	 */
	protected function type(): string
	{
		return Role::class;
	}
}
