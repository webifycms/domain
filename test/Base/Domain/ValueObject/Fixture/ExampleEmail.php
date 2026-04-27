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
use Webify\Base\Domain\ValueObject\Email;

/**
 * ExampleEmail is a concrete implementation of the Email value object for testing purposes.
 */
final readonly class ExampleEmail extends Email
{
	/**
	 * {@inheritDoc}
	 *
	 * Implementation of the domain specific validation rules for testing purposes.
	 */
	protected function validateDomainRules(string $value): void
	{
		[, $domain]     = explode('@', $value);
		$blockedDomains = ['tempmail.com', 'mailinator.com', 'guerrillamail.com'];

		if (in_array($domain, $blockedDomains, true)) {
			throw new InvalidArgumentException(
				'Disposable email addresses are not allowed.'
			);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function throwException(string $value): never
	{
		throw new InvalidArgumentException('Invalid email: ' . $value);
	}
}
