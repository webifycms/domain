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

use Webify\User\Identity\Domain\Exception\InvalidDisplayNameException;

/**
 * DisplayName value object represents the display name of a user.
 */
final readonly class DisplayName
{
	/**
	 * Allowed length.
	 */
	private const int MIN_LENGTH = 3;
	private const int MAX_LENGTH = 255;

	/**
	 * Private constructor enforces the use of the factory method.
	 *
	 * @param string $value the display name value to be associated with the instance
	 *
	 * @throws InvalidDisplayNameException if the provided display name is invalid
	 */
	private function __construct(private string $value)
	{
		if (!$this->isValid()) {
			throw InvalidDisplayNameException::create(self::MIN_LENGTH, self::MAX_LENGTH);
		}
	}

	/**
	 * Returns the string representation of the object.
	 */
	public function __toString(): string
	{
		return $this->value;
	}

	/**
	 * Creates an instance of the class from the given string.
	 */
	public static function fromString(string $value): self
	{
		return new self(trim($value));
	}

	/**
	 * Converts the object to its native string representation.
	 */
	public function toNative(): string
	{
		return $this->value;
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
		return '' !== $this->value
			&& mb_strlen($this->value) >= self::MIN_LENGTH
			&& mb_strlen($this->value) <= self::MAX_LENGTH;
	}
}
