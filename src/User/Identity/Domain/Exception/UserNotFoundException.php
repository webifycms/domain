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

use RuntimeException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when the user is not found.
 */
final class UserNotFoundException extends RuntimeException implements TranslatableExceptionInterface
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
	 * Factory method to create a UserNotFoundException for a specific user ID.
	 */
	public static function forId(string $id): UserNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.identity',
				'user_not_found',
				['id' => $id]
			),
			sprintf('User not found for id: %s.', $id)
		);
	}

	/**
	 * Factory method to create a UserNotFoundException for a specific user email.
	 */
	public static function forEmail(string $email): UserNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.identity',
				'user_not_found',
				['email' => $email]
			),
			sprintf('User not found for email: %s.', $email)
		);
	}
}
