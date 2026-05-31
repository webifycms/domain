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

namespace Webify\User\Authentication\Domain\ValueObject;

use Webify\Base\Domain\ValueObject\AggregateId;
use Webify\User\Authentication\Domain\Exception\InvalidUserIdException;

/**
 * User ID value object.
 */
final readonly class UserId extends AggregateId
{
	/**
	 * Throws an exception for an invalid user ID.
	 *
	 * @throws InvalidUserIdException if the user ID is invalid
	 */
	protected function throwException(string $value): never
	{
		throw InvalidUserIdException::forInvalidId($value);
	}
}
