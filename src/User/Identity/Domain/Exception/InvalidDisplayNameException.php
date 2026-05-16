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
 * Throws when an invalid user display name value is encountered.
 */
final class InvalidDisplayNameException extends InvalidArgumentException implements TranslatableExceptionInterface
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
	 * Factory method to initiate an InvalidDisplayNameException with a default message.
	 */
	public static function create(int $minLength, int $maxLength): self
	{
		return new self(
			new ExceptionTranslation(
				'user.identity',
				'invalid_display_name',
				[
					'min_length' => $minLength,
					'max_length' => $maxLength,
				]
			),
			sprintf('Invalid display name, allowed minimum and maximum length are: "%d-%d"', $minLength, $maxLength)
		);
	}
}
