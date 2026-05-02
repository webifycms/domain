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

namespace Webify\Base\Domain\Service\Authorization;

/**
 * RevokeRoleInterface defines the contract for revoking roles from subjects.
 *
 * Encapsulate the use case of removing a role assignment from a subject.
 * Revocation must be scoped to the same tenant context the assignment was created in — revoking globally
 * should not affect tenant-scoped assignments and vice versa.
 * The service is silent if the assignment does not exist (idempotent behaviour), so callers do not need to check first.
 */
interface RevokeRoleInterface
{
	/**
	 * Revoke the role from the given subject.
	 *
	 * @param string $roleAssignmentId the ID of the role assignment to be revoked
	 */
	public function revoke(string $roleAssignmentId): void;
}
