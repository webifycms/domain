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

use RuntimeException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when an authentication challenge is not found.
 */
final class ChallengeNotFoundException extends RuntimeException implements TranslatableExceptionInterface
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
	 * Creates a new instance for the given authentication challenge ID.
	 */
	public static function forId(string $id): ChallengeNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'challenge_not_found_for_id',
				['id' => $id]
			),
			sprintf('Authentication challenge not found for id: "%s"', $id)
		);
	}

	/**
	 * Creates a new instance for the given authentication challenge secret.
	 */
	public static function forSecret(string $token): ChallengeNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'challenge_not_found_for_secret',
				['token' => $token]
			),
			sprintf('Authentication challenge not found for secret: "%s"', $token)
		);
	}
}
