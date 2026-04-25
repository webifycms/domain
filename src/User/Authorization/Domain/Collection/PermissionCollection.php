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
use Webify\User\Authorization\Domain\Entity\Permission;

/**
 * Collection class for Permission entities.
 *
 * @extends Collection<Permission>
 */
final class PermissionCollection extends Collection
{
	/**
	 * {@inheritDoc}
	 */
	protected function type(): string
	{
		return Permission::class;
	}
}
