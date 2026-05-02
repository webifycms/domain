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

namespace Webify\Base\Domain\Service;

use DateTimeImmutable;

/**
 * RoleAssignInterface defines the contract for assigning roles to subjects.
 *
 * Encapsulate the use case of giving a subject a role, optionally scoped to a tenant and with optional expiry.
 * It validates that the role exists before creating the assignment, produces a clear
 * domain exception if it does not, and persists the assignment through the repository.
 *
 * Extension developers call this service when their extension needs to bootstrap
 * default role assignments (e.g. during installation).
 */
interface RoleAssignInterface
{
	/**
	 * Assign the role to the given subject.
	 *
	 * @param string                 $subjectId the ID of the subject to whom the role is assigned
	 * @param string                 $roleSlug  the slug of the role to be assigned
	 * @param null|string            $tenantId  the optional tenant ID for scoping the assignment
	 * @param null|DateTimeImmutable $expireAt  the optional expiration date and time for the assignment
	 */
	public function assign(
		string $subjectId,
		string $roleSlug,
		?string $tenantId = null,
		?DateTimeImmutable $expireAt = null
	): void;
}
