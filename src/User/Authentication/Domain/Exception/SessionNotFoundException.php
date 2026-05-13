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

use RuntimeException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when the session is not found.
 */
final class SessionNotFoundException extends RuntimeException implements TranslatableExceptionInterface
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
	 * Creates a new instance of SessionNotFoundException for the given session ID.
	 */
	public static function forId(string $id): SessionNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'session_not_found',
				['id' => $id]
			),
			sprintf('Session not found for id: "%s"', $id)
		);
	}

	/**
	 * Creates a new instance of SessionNotFoundException for the given access token.
	 */
	public static function forAccessToken(string $token): SessionNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'session_not_found',
				['token' => $token]
			),
			sprintf('Session not found for access token: "%s"', $token)
		);
	}

	/**
	 * Creates a new instance of SessionNotFoundException for the given refresh token.
	 */
	public static function forRefreshToken(string $token): SessionNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'session_not_found',
				['token' => $token]
			),
			sprintf('Session not found for refresh token: "%s"', $token)
		);
	}
}
