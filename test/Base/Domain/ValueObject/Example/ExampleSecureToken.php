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

namespace Webify\Test\Base\Domain\ValueObject\Example;

use InvalidArgumentException;
use Webify\Base\Domain\ValueObject\SecureToken;

/**
 * ExampleAggregateId is a concrete implementation of the AggregateId for testing purposes.
 *
 * It provides a simple implementation of the throwException method required by the abstract class.
 *
 * @internal
 */
final readonly class ExampleSecureToken extends SecureToken
{
	/**
	 * {@inheritDoc}
	 */
	public function throwException(string $value): never
	{
		throw new InvalidArgumentException('Invalid secure token: ' . $value);
	}
}
