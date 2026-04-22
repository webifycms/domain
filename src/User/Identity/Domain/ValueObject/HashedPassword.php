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

use Webify\User\Identity\Domain\Exception\InvalidPasswordHashException;

/**
 * Hashed password value object.
 */
final readonly class HashedPassword
{
	/**
	 * Private constructor enforces the use of the factory method.
	 *
	 * @param string $value the password hash value
	 *
	 * @throws InvalidPasswordHashException
	 */
	private function __construct(private string $value)
	{
		if (!$this->isValid()) {
			throw InvalidPasswordHashException::fromInvalidPasswordHash($this->value);
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
	 * Factory method to create a password hash object from a hash.
	 */
	public static function fromHash(string $value): self
	{
		return new self($value);
	}

	/**
	 * Checks if the password hash is equal to another password hash.
	 */
	public function equals(self $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Validates the password hash value.
	 */
	private function isValid(): bool
	{
		if (empty($this->value)) {
			return false;
		}

		return true;
	}
}
