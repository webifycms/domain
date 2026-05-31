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

namespace Webify\User\Authentication\Infrastructure\Exception;

use RuntimeException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when the authentication strategy is not found in the registry.
 */
final class StrategyNotFoundException extends RuntimeException implements TranslatableExceptionInterface
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
	 * Factory method to initiate this for a given identifier.
	 */
	public static function forIdentifier(string $identifier): StrategyNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.authentication',
				'strategy_not_found_for_identifier',
				['identifier' => $identifier]
			),
			sprintf('The authentication strategy with identifier "%s" is not registered.', $identifier)
		);
	}
}
