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

namespace Webify\Base\Domain\Contract\Authentication;

/**
 * StrategyInterface defines the contract for authentication strategies.
 *
 * This is the single seam for all authentication mechanisms — built-in (password, code, link)
 * and third-party authenticators.
 *
 * A strategy should cover only the INITIATION step of authentication.
 *
 * Third-party integrators register a concrete implementation through their extension's service provider.
 */
interface StrategyInterface
{
	/**
	 * Returns the unique identifier of the strategy.
	 *
	 * 1. This identifier is used to identify the strategy from the registry.
	 * 2. It is also used to identify the strategy in the authentication request.
	 */
	public function getIdentifier(): string;

	/**
	 * Checks if the strategy supports the given credentials.
	 */
	public function isSupported(Request $credentials): bool;

	/**
	 * Initiate the authentication for the user based on the provided credentials.
	 *
	 * @param Request         $requestCredentials the credentials used for authentication request
	 * @param UserCredentials $userCredentials    the user credentials to authenticate
	 */
	public function initiate(
		Request $requestCredentials,
		UserCredentials $userCredentials
	): void;
}
