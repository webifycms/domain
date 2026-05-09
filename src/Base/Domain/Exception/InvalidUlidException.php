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

namespace Webify\Base\Domain\Exception;

use InvalidArgumentException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when an invalid ULID is encountered.
 */
final class InvalidUlidException extends InvalidArgumentException implements TranslatableExceptionInterface
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
	 * Factory method to create an exception for an invalid ULID.
	 *
	 * @param string $ulid the ULID string that was deemed invalid
	 */
	public static function forInvalidUlid(string $ulid): InvalidUlidException
	{
		return new self(
			new ExceptionTranslation(
				'base.ulid',
				'invalid_ulid',
				['ulid' => $ulid]
			),
			sprintf('Invalid ULID "%s" given for normalization', $ulid)
		);
	}
}
