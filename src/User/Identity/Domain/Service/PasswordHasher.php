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

namespace Webify\User\Identity\Domain\Service;

use Webify\User\Identity\Domain\Exception\FailedToHashPasswordException;
use Webify\User\Identity\Domain\ValueObject\PasswordHash;

/**
 * Password hash domain service.
 */
final readonly class PasswordHasher
{
	/**
	 * Generates a hashed string from the provided text password.
	 *
	 * @param string $password the password to hash
	 *
	 * @return PasswordHash the resulting hashed string
	 */
	public function hash(string $password): PasswordHash
	{
		$hash = password_hash($password, PASSWORD_DEFAULT);

		if (empty($hash)) {
			throw new FailedToHashPasswordException();
		}

		return PasswordHash::fromHash($hash);
	}

	/**
	 * Verifies whether the provided password matches the hashed password.
	 *
	 * @param string $password the plaintext password to verify
	 * @param string $hash     an object representing the hashed password
	 *
	 * @return bool returns true if the password matches the hash, false otherwise
	 */
	public function verify(string $password, string $hash): bool
	{
		$hashed = PasswordHash::fromHash($hash);

		return password_verify($password, $hashed->toNative());
	}
}
