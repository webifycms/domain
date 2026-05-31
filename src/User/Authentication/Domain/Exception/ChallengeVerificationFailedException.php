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
 * Thrown when an authentication challenge fails to verify.
 */
final class ChallengeVerificationFailedException extends DomainException implements TranslatableExceptionInterface
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
	 * Factory method to create an exception for an expired verification code or token supplied by the user.
	 */
	public static function forExpired(string $type): ChallengeVerificationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'challenge_expired',
				['type' => $type]
			),
			sprintf('Authentication challenge verification failed due to expired %s.', $type)
		);
	}

	/**
	 * Factory method to create an exception for an already verified code or token supplied by the user.
	 */
	public static function forAlreadyVerified(string $type): ChallengeVerificationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'challenge_already_verified',
				['type' => $type]
			),
			sprintf('The authentication challenge %s has already been verified.', $type)
		);
	}

	/**
	 * Factory method to create an exception for maximum attempts reached.
	 */
	public static function forMaximumAttempts(string $type): ChallengeVerificationFailedException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'challenge_maximum_attempts_reached',
			),
			sprintf('Maximum attempts reached for authentication challenge %s.', $type)
		);
	}
}
