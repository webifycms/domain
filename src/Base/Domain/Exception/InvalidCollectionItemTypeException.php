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
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when an item of an invalid type is added to a collection.
 */
final class InvalidCollectionItemTypeException extends InvalidArgumentException implements TranslatableExceptionInterface
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
	 * Factory method for creating an instance of the exception when an invalid type is encountered.
	 *
	 * @param string $classname    the name of the collection class where the type violation occurred
	 * @param string $expectedType the expected type of the item
	 * @param string $givenType    the actual type of the item that was provided
	 *
	 * @return InvalidCollectionItemTypeException an instance of the exception configured for the invalid type scenario
	 */
	public static function forInvalidItemType(
		string $classname,
		string $expectedType,
		string $givenType,
	): InvalidCollectionItemTypeException {
		return new self(
			new ExceptionTranslation(
				'base.domain',
				'invalid_type_of_collection',
				[
					'class'        => $classname,
					'expectedType' => $expectedType,
					'givenType'    => $givenType,
				]
			),
			sprintf(
				'%s only accepts items of type %s, got %s.',
				$classname,
				$expectedType,
				$givenType
			),
		);
	}
}
