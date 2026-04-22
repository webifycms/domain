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
use Webify\Base\Domain\Exception\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when the password hashing process fails.
 */
final class FailedToHashPasswordException extends RuntimeException implements TranslatableExceptionInterface
{
	/**
	 * The translation object for this exception.
	 */
	public ExceptionTranslation $translation {
		get {
			return $this->translation;
		}
	}

	/**
	 * The constructor.
	 */
	public function __construct(string $message = 'Failed to hash password')
	{
		parent::__construct($message);

		$this->translation = new ExceptionTranslation(
			'user.identity',
			'failed_to_hash_password'
		);
	}
}
