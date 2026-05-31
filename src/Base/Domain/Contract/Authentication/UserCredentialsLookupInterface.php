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
 * Anti-corruption contract for user credential lookup.
 *
 * Allows the Authentication BC to verify user credentials and retrieve the minimum required information
 * for session creation without depending on the User Identity BC.
 */
interface UserCredentialsLookupInterface
{
	/**
	 * Searches for a user credential by the provided email address.
	 *
	 * @param string $email the email address to search for
	 *
	 * @return null|UserCredentials returns the UserCredential object if found, or null if no match is found
	 */
	public function findByEmail(string $email): ?UserCredentials;
}
