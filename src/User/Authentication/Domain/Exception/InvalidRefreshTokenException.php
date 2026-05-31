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

namespace Webify\User\Authentication\Domain\Exception;

use InvalidArgumentException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Exception thrown when an invalid refresh token is encountered.
 */
final class InvalidRefreshTokenException extends InvalidArgumentException implements TranslatableExceptionInterface
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 *
	 * @param ExceptionTranslation $translation the translation object for this exception
	 * @param string               $message     the exception message (optional)
	 */
	private function __construct(
		public readonly ExceptionTranslation $translation,
		string $message = ''
	) {
		parent::__construct($message);
	}

	/**
	 * Factory method to initiate this with a default message.
	 */
	public static function forInvalidRefreshToken(string $token): InvalidRefreshTokenException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'invalid_refresh_token',
				['token' => $token]
			),
			sprintf('The refresh token "%s" is invalid.', $token)
		);
	}
}
