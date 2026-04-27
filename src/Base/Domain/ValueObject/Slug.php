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

namespace Webify\Base\Domain\ValueObject;

/**
 * Slug value object base class.
 */
abstract readonly class Slug
{
	/**
	 * Slug pattern format:
	 * - Must be lowercase.
	 * - Must contain only letters, numbers, and hyphens.
	 * - Must not start or end with a hyphen.
	 * - Must not contain consecutive hyphens.
	 */
	private const string PATTERN_FORMAT = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

	/**
	 * The closed constructor enforces to use factory methods to initiate this object.
	 */
	final public function __construct(private string $value)
	{
		if (!$this->isValid()) {
			$this->throwException($this->value);
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
	 * Factory method to create a slug value object from a string.
	 */
	public static function fromString(string $value): static
	{
		return new static($value);
	}

	/**
	 * Converts the object to its native string representation.
	 */
	public function toNative(): string
	{
		return $this->value;
	}

	/**
	 * Compares the current object with another instance for equality.
	 *
	 * @param Slug $other the instance to compare with the current object
	 *
	 * @return bool true if both instances are considered equal, false otherwise
	 */
	public function equals(self $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Throws an exception as part of the implementation detail in derived classes.
	 */
	abstract protected function throwException(string $value): void;

	/**
	 * Validates the value against a predefined format pattern.
	 */
	private function isValid(): bool
	{
		return (bool) preg_match(self::PATTERN_FORMAT, $this->value);
	}
}
