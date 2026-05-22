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
use Throwable;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Exception thrown when an invalid access token is encountered.
 */
final class ChallengeCodeGenerationFailedException extends RuntimeException implements TranslatableExceptionInterface
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 *
	 * @param ExceptionTranslation $translation the translation object for this exception
	 * @param string               $message     the exception message (optional)
	 * @param Throwable            $previous    the previous throwable used for the exception chaining (optional)
	 */
	private function __construct(
		public readonly Throwable $previous,
		public readonly ExceptionTranslation $translation,
		string $message = '',
	) {
		parent::__construct($message, 0, $previous);
	}

	/**
	 * Factory method to initiate this with a default message.
	 */
	public static function create(Throwable $throwable): ChallengeCodeGenerationFailedException
	{
		return new self(
			$throwable,
			new ExceptionTranslation(
				'user.authentication',
				'challenge_code_generation_failed',
			),
			'Unable to generate authentication challenge code.',
		);
	}
}
