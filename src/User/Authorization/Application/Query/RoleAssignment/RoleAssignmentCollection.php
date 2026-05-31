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

use Webify\Base\Contracts\Collection\Collection;

/**
 * Collection class for RoleAssignment read model.
 *
 * @extends Collection<RoleAssignment>
 */
final class RoleAssignmentCollection extends Collection
{
	/**
	 * {@inheritDoc}
	 */
	protected function type(): string
	{
		return RoleAssignment::class;
	}
}
