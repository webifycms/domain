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

use Webify\Base\Domain\ValueObject\Email;
use Webify\User\Identity\Domain\Exception\InvalidUserEmailException;

/**
 * User email value object.
 */
final readonly class UserEmail extends Email
{
	/**
	 * {@inheritDoc}
	 *
	 * We don't have any domain specific validation for user emails and leaving this method empty.
	 */
	protected function validateDomainRules(string $value): void {}

	/**
	 * {@inheritDoc}
	 */
	protected function throwException(string $value): never
	{
		throw InvalidUserEmailException::fromInvalidEmail($value);
	}
}
