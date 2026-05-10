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

namespace Webify\User\Authentication\Domain\ValueObject;

use Webify\Base\Domain\ValueObject\SecureToken;
use Webify\User\Authentication\Domain\Exception\InvalidRefreshTokenException;

/**
 * Refresh token value object.
 */
final readonly class RefreshToken extends SecureToken
{
	/**
	 * Throws an exception for an invalid access token.
	 *
	 * @throws InvalidRefreshTokenException if the access token is invalid
	 */
	public function throwException(string $value): never
	{
		throw InvalidRefreshTokenException::forInvalidRefreshToken($value);
	}
}
