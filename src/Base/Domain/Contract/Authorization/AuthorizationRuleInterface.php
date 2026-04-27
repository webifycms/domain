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

namespace Webify\Base\Domain\Contract\Authorization;

/**
 * AuthorizationRuleInterface defines the contract of authorization rule.
 *
 * This is the primary extension point for conditional access logic.
 * A Rule answers the question "even though the role permits this action, are the surrounding conditions satisfied?"
 * Rules are additive — all active rules registered on the authorization service must return true
 * for access to be granted.
 */
interface AuthorizationRuleInterface
{
	/**
	 * Determines whether the given subject is satisfied with the provided resource.
	 *
	 * @param AuthorizableSubjectInterface  $subject  the subject being evaluated
	 * @param AuthorizableResourceInterface $resource the resource being checked for satisfaction
	 *
	 * @return bool true if the subject is satisfied with the resource, otherwise false
	 */
	public function isSatisfied(AuthorizableSubjectInterface $subject, AuthorizableResourceInterface $resource): bool;
}
