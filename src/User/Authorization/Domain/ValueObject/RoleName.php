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

use Webify\User\Authorization\Domain\Exception\InvalidRoleNameException;

/**
 * Role name value object.
 */
final readonly class RoleName
{
	/**
	 * Private constructor enforces the use of the factory method.
	 *
	 * @param string $name the name to be associated with the instance
	 *
	 * @throws InvalidRoleNameException if the provided name is invalid
	 */
	private function __construct(private string $name)
	{
		if (!$this->isValid()) {
			throw InvalidRoleNameException::fromInvalidName($this->name);
		}
	}

	/**
	 * Returns the string representation of the object.
	 */
	public function __toString(): string
	{
		return $this->name;
	}

	/**
	 * Creates an instance of the class from the given string.
	 */
	public static function fromString(string $name): self
	{
		return new self($name);
	}

	/**
	 * Converts the object to its native string representation.
	 */
	public function toNative(): string
	{
		return $this->name;
	}

	/**
	 * Determines if the current object is equal to the specified object.
	 *
	 * @param self $other the object to compare with the current object
	 *
	 * @return bool true if the objects are equal, false otherwise
	 */
	public function equals(self $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Determines if the current object state is valid.
	 *
	 * @return bool true if valid, false otherwise
	 */
	private function isValid(): bool
	{
		if ('' === $this->name) {
			return false;
		}

		return true;
	}
}
