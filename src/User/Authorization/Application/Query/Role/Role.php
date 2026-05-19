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
 * The Role read model represents raw data of a role entity.
 *
 * @todo Permissions type should be decided later after the persistence layer is implemented.
 */
final readonly class Role
{
	/**
	 * The constructor.
	 *
	 * @param array<array{
	 *      scope: string,
	 *      action: string,
	 *      resource: string,
	 *  }> $permissions
	 */
	public function __construct(
		public string $id,
		public string $name,
		public string $slug,
		public array $permissions,
		public bool $isSystemRole
	) {}
}
