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
 * Exception thrown when invalid permission properties are encountered.
 */
final class InvalidPermissionException extends InvalidArgumentException implements TranslatableExceptionInterface
{
	/**
	 * {@inheritDoc}
	 */
	public ExceptionTranslation $translation {
		get {
			return $this->translation;
		}
	}

	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 *
	 * @param string $message the exception message (optional)
	 */
	public function __construct(
		string $message = 'Invalid permission properties.'
	) {
		parent::__construct($message);

		$this->translation = new ExceptionTranslation(
			'user.authorization',
			'invalid_permission'
		);
	}
}
