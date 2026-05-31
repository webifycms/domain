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
 * Exception thrown when invalid user credential is encountered.
 */
final class AuthenticationFailedException extends InvalidArgumentException implements TranslatableExceptionInterface
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
	 * Factory method to initiate this when an email is missing.
	 */
	public static function forMissingEmail(): AuthenticationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'email_required_for_authentication',
			),
			'Email is required for authentication.'
		);
	}

	/**
	 * Factory method to initiate this when a password is missing.
	 */
	public static function forMissingPassword(): AuthenticationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'password_required_for_authentication',
			),
			'Password is required for authentication.'
		);
	}

	/**
	 * Factory method to initiate this when a secret is missing.
	 */
	public static function forMissingSecret(): AuthenticationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'secret_required_for_authentication',
			),
			'Secret is required for passwordless authentication.'
		);
	}

	/**
	 * Factory method to initiate this when invalid user credentials are encountered.
	 */
	public static function forInvalid(): AuthenticationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'invalid_user_credentials',
			),
			'Invalid user credentials.'
		);
	}

	/**
	 * Factory method to initiate this when a user is not found.
	 *
	 * @param string $email the email used to authenticate
	 */
	public static function userNotFound(string $email): AuthenticationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'user_not_found',
				['email' => $email]
			),
			sprintf('User with email "%s" not found.', $email)
		);
	}

	/**
	 * Factory method to initiate this when a user is not active.
	 */
	public static function userNotActive(): AuthenticationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'user_not_active'
			),
			'User is not active.'
		);
	}

	/**
	 * Factory method to initiate this when an authentication strategy is not supported or not found.
	 */
	public static function strategyNotSupported(string $strategyIdentifier): AuthenticationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'strategy_not_supported',
				['strategy' => $strategyIdentifier]
			),
			sprintf('Authentication strategy "%s" is not supported.', $strategyIdentifier)
		);
	}
}
