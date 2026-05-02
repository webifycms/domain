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

/**
 * AuthorizationInterface defines the contract for authorization service.
 *
 * The single contract that the entire application depends on for access checks.
 * This is the most important interface in the bounded context — every application service,
 * every console command, and every event handler that needs to enforce access calls this and nothing else.
 */
interface AuthorizationInterface
{
	/**
	 * Checks whether the given subject is authorized to access the specified resource.
	 *
	 * @param string                        $action   the action being checked for authorization
	 * @param AuthorizableSubjectInterface  $subject  the subject whose authorization is being evaluated
	 * @param AuthorizableResourceInterface $resource the resource for which access is being checked
	 *
	 * @return bool returns true if the subject is authorized to access the resource, false otherwise
	 */
	public function check(
		string $action,
		AuthorizableSubjectInterface $subject,
		AuthorizableResourceInterface $resource
	): bool;
}
