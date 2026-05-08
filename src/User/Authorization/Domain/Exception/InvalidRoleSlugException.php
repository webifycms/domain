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

namespace Webify\User\Authorization\Domain\Exception;

use InvalidArgumentException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Exception thrown when an invalid role slug value is encountered.
 */
final class InvalidRoleSlugException extends InvalidArgumentException implements TranslatableExceptionInterface
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
	 *
	 * @param string $value the invalid role slug value
	 */
	public static function forInvalidSlug(string $value): self
	{
		return new self(
			new ExceptionTranslation(
				'user.authorization',
				'invalid_role_slug',
				['value' => $value]
			),
			sprintf('Invalid role slug "%s"', $value)
		);
	}

	/**
	 * Creates an instance of the exception for an invalid role slug separator.
	 *
	 * @param string $value the slug with separator that is considered invalid
	 */
	public static function forInvalidSeparator(string $value): self
	{
		return new self(
			new ExceptionTranslation(
				'user.authorization',
				'invalid_role_slug',
				['value' => $value]
			),
			sprintf('Invalid role slug separator "%s"', $value)
		);
	}
}
