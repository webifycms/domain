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
use Webify\Base\Domain\Exception\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Exception thrown when an invalid tenant id value is encountered.
 */
final class InvalidTenantIdException extends InvalidArgumentException implements TranslatableExceptionInterface
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
	 * Factory method to initiate an InvalidTenantIdException with a default message.
	 *
	 * @param string $value the invalid tenant id value
	 */
	public static function fromInvalidId(string $value): self
	{
		return new self(
			new ExceptionTranslation(
				'user.authorization',
				'invalid_tenant_id',
				['value' => $value]
			),
			sprintf('Invalid tenant id "%s"', $value)
		);
	}
}
