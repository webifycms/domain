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

use Webify\Base\Domain\Contract\Authorization\{AuthorizableResourceInterface, AuthorizationRuleInterface};

/**
 * AuthorizationRuleRegistryInterface defines the contract for authorization rule registry service.
 */
interface AuthorizationRuleRegistryInterface
{
	/**
	 * Adds an authorization rule to the system.
	 *
	 * @param AuthorizationRuleInterface $rule the authorization rule to be added
	 */
	public function add(AuthorizationRuleInterface $rule): void;

	/**
	 * Retrieves all available rules.
	 *
	 * @return AuthorizationRuleInterface[] an array containing all rules
	 */
	public function getAll(): array;

	/**
	 * Retrieves all applicable rules for the given resource.
	 *
	 * @param AuthorizableResourceInterface $resource the resource for which applicable rules are to be retrieved
	 *
	 * @return AuthorizationRuleInterface[] an array of policies applicable to the provided resource
	 */
	public function getAllApplicableTo(AuthorizableResourceInterface $resource): array;
}
