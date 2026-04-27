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

use Webify\Base\Domain\ValueObject\Slug;
use Webify\User\Authorization\Domain\Exception\InvalidRoleSlugException;

/**
 * Role slug value object.
 */
final readonly class RoleSlug extends Slug
{
	/**
	 * Throws an exception if the value is not valid.
	 *
	 * @throws InvalidRoleSlugException
	 */
	protected function throwException(string $value): void
	{
		throw InvalidRoleSlugException::fromInvalidSlug($value);
	}
}
