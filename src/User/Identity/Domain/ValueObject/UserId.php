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

namespace Webify\User\Identity\Domain\ValueObject;

use Webify\Base\Domain\ValueObject\AggregateId;
use Webify\User\Identity\Domain\Exception\InvalidUserIdException;

/**
 * The user ID value object.
 */
final readonly class UserId extends AggregateId
{
	/**
	 * Will throw an exception if the user id value is not valid.
	 *
	 * @throws InvalidUserIdException
	 */
	public function throwException(string $value): void
	{
		throw InvalidUserIdException::fromDefault($value);
	}
}
