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

namespace Webify\User\Identity\Domain\Exception;

use DomainException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when the user's current password is not strong enough.
 */
final class WeakPasswordException extends DomainException implements TranslatableExceptionInterface
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
	 * Creates an exception indicating that the provided password is too short.
	 *
	 * @param int $minLength the minimum required length for the password
	 */
	public static function tooShort(int $minLength): self
	{
		return new self(
			new ExceptionTranslation('user.identity', 'password_too_short', ['length' => $minLength]),
			sprintf('Password must be at least %d characters long.', $minLength)
		);
	}

	/**
	 * Creates an exception for passwords that exceed the maximum allowed length.
	 *
	 * @param int $maxLength the maximum permitted length for the password
	 */
	public static function tooLong(int $maxLength): self
	{
		return new self(
			new ExceptionTranslation('user.identity', 'password_too_long', ['length' => $maxLength]),
			sprintf('Password must not exceed %d characters.', $maxLength)
		);
	}

	/**
	 * Creates an instance of this exception indicating that the password is missing an uppercase letter.
	 */
	public static function missingUppercase(): self
	{
		return new self(
			new ExceptionTranslation('user.identity', 'password_missing_uppercase'),
			'Password must contain at least one uppercase letter.'
		);
	}

	/**
	 * Creates an instance of this exception indicating that the password is missing an lowercase letter.
	 */
	public static function missingLowercase(): self
	{
		return new self(
			new ExceptionTranslation('user.identity', 'password_missing_lowercase'),
			'Password must contain at least one lowercase letter.'
		);
	}

	/**
	 * Creates an instance of this exception indicating that the password is missing a digit.
	 */
	public static function missingDigit(): self
	{
		return new self(
			new ExceptionTranslation('user.identity', 'password_missing_digit'),
			'Password must contain at least one digit.'
		);
	}

	/**
	 * Creates an instance of this exception indicating that the password is missing a special character.
	 */
	public static function missingSpecialChar(): self
	{
		return new self(
			new ExceptionTranslation('user.identity', 'password_missing_special_char'),
			'Password must contain at least one special character.'
		);
	}
}
