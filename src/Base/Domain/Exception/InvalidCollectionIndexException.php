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

use OutOfBoundsException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when trying to access an index that does not exist in the collection.
 */
final class InvalidCollectionIndexException extends OutOfBoundsException implements TranslatableExceptionInterface
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
	 * Factory method to create an instance of the exception with a specific message and context.
	 *
	 * @param string $classname the name of the class where the collection exists
	 * @param int    $index     the index in the collection that does not exist
	 *
	 * @return InvalidCollectionIndexException an instance of the exception configured with the provided context
	 */
	public static function forMissingIndex(string $classname, int $index): InvalidCollectionIndexException
	{
		return new self(
			new ExceptionTranslation(
				'base.domain',
				'not_exist_in_collection',
				[
					'index' => $index,
					'class' => $classname,
				]
			),
			sprintf('Index %d does not exist in %s.', $index, $classname),
		);
	}
}
