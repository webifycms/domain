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

/**
 * Thrown when an item of an invalid type is added to a collection.
 */
final class InvalidTypeOfCollectionException extends InvalidArgumentException implements TranslatableExceptionInterface
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
	 * The constructor.
	 *
	 * @param string $message the exception message
	 * @param array{
	 *     class: string,
	 *     expectedType: string,
	 *     givenType: string,
	 * } $parameters the translation parameters
	 */
	public function __construct(
		string $message,
		array $parameters
	) {
		parent::__construct($message);

		$this->translation = new ExceptionTranslation(
			'base.domain',
			'invalid_type_of_collection',
			$parameters
		);
	}
}
