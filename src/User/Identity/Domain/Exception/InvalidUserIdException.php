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
use Webify\Base\Domain\Exception\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Exception thrown when an invalid user id value is encountered.
 */
final class InvalidUserIdException extends InvalidArgumentException implements TranslatableExceptionInterface
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 *
	 * @param ExceptionTranslation $translation the translation object for this exception
	 * @param string               $message     the exception message (optional)
	 */
	private function __construct(
		private readonly ExceptionTranslation $translation,
		string $message = ''
	) {
		parent::__construct($message);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTranslation(): ExceptionTranslation
	{
		return $this->translation;
	}

	/**
	 * Factory method to initiate an InvalidUserIdException with a default message.
	 *
	 * @param string $value the invalid user id value
	 */
	public static function fromDefault(string $value): InvalidUserIdException
	{
		return new self(
			new ExceptionTranslation(
				'user.identity',
				'invalid_user_id',
				['value' => $value]
			),
			sprintf('Invalid user id "%s"', $value)
		);
	}
}
