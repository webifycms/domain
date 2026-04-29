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

use DateTimeImmutable;

/**
 * This class is a read model for role assignment.
 */
final readonly class RoleAssignment
{
	/**
	 * Constructor method for initializing the object with specified parameters.
	 *
	 * @param string                 $id        identifier
	 * @param string                 $roleId    role identifier
	 * @param string                 $subjectId subject identifier
	 * @param null|string            $tenantId  Optional tenant identifier. Default is null.
	 * @param null|DateTimeImmutable $expiresAt Optional expiration date and time. Default is null.
	 */
	public function __construct(
		public string $id,
		public string $roleId,
		public string $subjectId,
		public ?string $tenantId = null,
		public ?DateTimeImmutable $expiresAt = null
	) {}
}
