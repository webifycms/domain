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
use Webify\User\Authentication\Domain\Exception\InvalidSessionIdException;

/**
 * The token ID value object.
 *
 * Define a typed identity for an authentication session token.
 * Reuses the AggregateId base class to get ULID validation.
 */
final readonly class SessionId extends AggregateId
{
	/**
	 * Will throw an exception if the token id value is not valid.
	 *
	 * @throws InvalidSessionIdException
	 */
	protected function throwException(string $value): never
	{
		throw InvalidSessionIdException::forInvalidId($value);
	}
}
