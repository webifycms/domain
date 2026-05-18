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

namespace Webify\User\Authorization\Domain\Query\RoleAssignment;

use DateTimeImmutable;

/**
 * The RoleAssignment read modal represents raw data of a role assignment entity.
 */
final readonly class RoleAssignment
{
	/**
	 * The constructor.
	 */
	public function __construct(
		public string $id,
		public string $roleId,
		public string $subjectId,
		public ?string $tenantId = null,
		public ?DateTimeImmutable $expiresAt = null
	) {}
}
