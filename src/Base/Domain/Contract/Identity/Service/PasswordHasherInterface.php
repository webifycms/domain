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

namespace Webify\Base\Domain\Contract\Identity\Service;

/**
 * The contract for representing a password hashing service.
 *
 * Provides methods for generating hashed passwords and verifying plaintext passwords against hashed values.
 */
interface PasswordHasherInterface
{
	/**
	 * Generates a hashed string from the provided text password.
	 *
	 * @param string $password the password to hash
	 *
	 * @return string the resulting hashed string
	 */
	public function hash(string $password): string;

	/**
	 * Verifies whether the provided password matches the hashed password.
	 *
	 * @param string $password the plaintext password to verify
	 * @param string $hash     the hash representing the hashed password
	 *
	 * @return bool returns true if the password matches the hash, false otherwise
	 */
	public function verify(string $password, string $hash): bool;
}
