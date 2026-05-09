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

use InvalidArgumentException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Exception thrown when an invalid password hash value is encountered.
 */
final class InvalidPasswordHashException extends InvalidArgumentException implements TranslatableExceptionInterface
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
	 * Factory method to initiate an InvalidPasswordHashException with a default message.
	 *
	 * @param string $hash the invalid password hash value
	 */
	public static function forInvalidPasswordHash(string $hash): self
	{
		return new self(
			new ExceptionTranslation(
				'user.identity',
				'invalid_password_hash',
				['hash' => $hash]
			),
			sprintf('Invalid password hash: "%s"', $hash)
		);
	}
}
