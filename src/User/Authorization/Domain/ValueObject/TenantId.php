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

namespace Webify\User\Authorization\Domain\ValueObject;

use Webify\Base\Domain\ValueObject\AggregateId;
use Webify\User\Authorization\Domain\Exception\InvalidTenantIdException;

/**
 * Tenant ID value object.
 */
final readonly class TenantId extends AggregateId
{
	/**
	 * Throws an exception if the value is not valid.
	 *
	 * @throws InvalidTenantIdException
	 */
	protected function throwException(string $value): void
	{
		throw InvalidTenantIdException::fromInvalidId($value);
	}
}
