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
 * Exception thrown when an invalid subject id value is encountered.
 */
final class InvalidSubjectAttributesException extends InvalidArgumentException implements TranslatableExceptionInterface
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
		string $message = 'Invalid subject attributes, the attribute must be of strings of key and value.'
	) {
		parent::__construct($message);

		$this->translation = new ExceptionTranslation(
			'user.authorization',
			'invalid_subject_attributes',
			[]
		);
	}
}
