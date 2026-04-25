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

use Webify\User\Authorization\Domain\Exception\InvalidSubjectTypeException;

/**
 * Subject type value object.
 */
final readonly class SubjectType
{
	/**
	 * The constructor.
	 */
	public function __construct(private string $value)
	{
		if (!$this->isValid()) {
			throw InvalidSubjectTypeException::fromInvalidType($this->value);
		}
	}

	/**
	 * Converts the object to its string representation.
	 */
	public function __toString(): string
	{
		return $this->value;
	}

	/**
	 * Get the native string value of the password hash.
	 */
	public function toNative(): string
	{
		return $this->value;
	}

	/**
	 * Checks if the password hash is equal to another password hash.
	 */
	public function equals(self $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Checks whether the current value meets the validation criteria.
	 *
	 * @return bool true if the value is valid, false otherwise
	 */
	private function isValid(): bool
	{
		if ('' === $this->value) {
			return false;
		}

		return true;
	}
}
