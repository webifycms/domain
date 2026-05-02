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

use Webify\Base\Domain\Contract\Authorization\{AuthorizableResourceInterface, AuthorizableSubjectInterface};
use Webify\Base\Domain\Exception\AccessDeniedException;

/**
 * CheckAccessInterface defines the contract for checking access permissions.
 *
 * This is the single point of entry for all access checks in the system,
 * ensuring a consistent approach to authorization across the application.
 * It adds deny-and-throw convenience needed in 95% of cases, avoiding duplicated throw logic.
 */
interface CheckAccessInterface
{
	/**
	 * Checks if the given subject has permission to perform the specified action on the resource.
	 *
	 * @param string                        $action   the action being performed
	 * @param AuthorizableSubjectInterface  $subject  the subject performing the action
	 * @param AuthorizableResourceInterface $resource the resource being accessed
	 *
	 * @return bool true if access is allowed, false otherwise
	 */
	public function isAllowed(
		string $action,
		AuthorizableSubjectInterface $subject,
		AuthorizableResourceInterface $resource
	): bool;

	/**
	 * Checks if the given subject has permission to perform the specified action on the resource,
	 * and throws an `AccessDeniedException` if access is denied.
	 *
	 * @param string                        $action   the action being performed
	 * @param AuthorizableSubjectInterface  $subject  the subject performing the action
	 * @param AuthorizableResourceInterface $resource the resource being accessed
	 *
	 * @throws AccessDeniedException if access is denied
	 */
	public function denyUnless(
		string $action,
		AuthorizableSubjectInterface $subject,
		AuthorizableResourceInterface $resource
	): void;
}
