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
use Webify\User\Authentication\Domain\Exception\InvalidChallengeTokenException;

/**
 * Challenge token value object.
 */
final readonly class ChallengeToken extends SecureToken
{
	/**
	 * Throws an exception for an invalid challenge token.
	 */
	public function throwException(string $value): never
	{
		throw InvalidChallengeTokenException::forInvalidToken($value);
	}
}
