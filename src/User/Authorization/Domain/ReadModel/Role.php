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

namespace Webify\User\Authorization\Domain\ReadModel;

use Webify\User\Authorization\Domain\Collection\PermissionCollection;

/**
 * Role read model.
 */
final readonly class Role
{
	/**
	 * The constructor.
	 */
	public function __construct(
		public string $id,
		public string $name,
		public string $slug,
		public PermissionCollection $permissions,
		public bool $isSystemRole
	) {}
}
