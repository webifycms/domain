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

namespace Webify\Base\Domain\Contract\Authorization\Service;

use Webify\Base\Domain\Contract\Authorization\{AuthorizableResourceInterface, RuleInterface};

/**
 * RuleRegistryInterface defines the contract for authorization rule registry service.
 */
interface RuleRegistryInterface
{
	/**
	 * Register an authorization rule to the system.
	 *
	 * @param RuleInterface $rule the authorization rule to be added
	 */
	public function register(RuleInterface $rule): void;

	/**
	 * Retrieves all available rules.
	 *
	 * @return RuleInterface[] an array containing all rules
	 */
	public function getAll(): array;

	/**
	 * Retrieves all applicable rules for the given resource.
	 *
	 * @param AuthorizableResourceInterface $resource the resource for which applicable rules are to be retrieved
	 *
	 * @return RuleInterface[] an array of policies applicable to the provided resource
	 */
	public function getAllApplicableTo(AuthorizableResourceInterface $resource): array;
}
