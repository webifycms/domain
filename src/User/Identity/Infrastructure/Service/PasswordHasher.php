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

namespace Webify\User\Identity\Infrastructure\Service;

use ValueError;
use Webify\Base\Domain\Contract\Identity\PasswordHasherInterface;
use Webify\User\Identity\Infrastructure\Exception\FailedToHashPasswordException;

/**
 * The implementation for the password hashing service.
 */
final readonly class PasswordHasher implements PasswordHasherInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function hash(string $password): string
	{
		try {
			return password_hash($password, PASSWORD_DEFAULT);
		} catch (ValueError $throwable) {
			throw FailedToHashPasswordException::create($throwable);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function verify(string $password, string $hash): bool
	{
		return password_verify($password, $hash);
	}
}
