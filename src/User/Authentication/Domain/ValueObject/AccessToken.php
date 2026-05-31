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

use Webify\Base\Domain\Exception\SecureTokenGenerationFailedException;
use Webify\Base\Domain\ValueObject\SecureToken;
use Webify\User\Authentication\Domain\Exception\{AccessTokenGenerationFailedException, InvalidAccessTokenException};

/**
 * Access token value object.
 */
final readonly class AccessToken extends SecureToken
{
	/**
	 * Overrides the parent method to handle the exception to make a domain-specific one.
	 *
	 * @throws AccessTokenGenerationFailedException
	 */
	public static function generate(): static
	{
		try {
			return parent::generate();
		} catch (SecureTokenGenerationFailedException $exception) {
			throw AccessTokenGenerationFailedException::create($exception);
		}
	}

	/**
	 * Throws an exception for an invalid access token.
	 *
	 * @throws InvalidAccessTokenException if the access token is invalid
	 */
	public function throwException(string $value): never
	{
		throw InvalidAccessTokenException::forInvalidAccessToken($value);
	}
}
