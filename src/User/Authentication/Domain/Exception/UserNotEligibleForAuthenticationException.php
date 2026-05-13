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

namespace Webify\User\Authentication\Domain\Exception;

use DomainException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when a user is not eligible for authentication.
 */
final class UserNotEligibleForAuthenticationException extends DomainException implements TranslatableExceptionInterface
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
	 * Factory method to create an exception for cases where a user is not eligible for authentication
	 * based on the given status.
	 *
	 * @param string $status the status indicating why the user is not eligible
	 */
	public static function forStatus(string $status): UserNotEligibleForAuthenticationException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'user_not_eligible_for_authentication',
				['status' => $status]
			),
			sprintf('User is not eligible for authentication with the status: %s', $status)
		);
	}
}
