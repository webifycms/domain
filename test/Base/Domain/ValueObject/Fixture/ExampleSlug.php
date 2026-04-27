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

namespace Webify\Test\Base\Domain\ValueObject\Fixture;

use InvalidArgumentException;
use Webify\Base\Domain\ValueObject\Slug;

/**
 * ExampleSlug is a concrete implementation of the Slug value object for testing purposes.
 */
final readonly class ExampleSlug extends Slug
{
	/**
	 * {@inheritDoc}
	 */
	protected function throwException(string $value): void
	{
		throw new InvalidArgumentException('Invalid example slug: ' . $value);
	}
}
