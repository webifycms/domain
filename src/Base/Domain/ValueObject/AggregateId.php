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
 * The aggregate ID value object base class.
 *
 * This only accepts ULIDs that stand for Universally Unique Lexicographically Sortable Identifier,
 * which is a 128-bit identifier designed to be unique and sortable based on the time of creation.
 * It combines a timestamp with a random component, allowing for efficient indexing and retrieval
 * in databases and distributed systems.
 */
abstract readonly class AggregateId
{
	/**
	 * The ULID specification regex pattern:
	 * - Must be exactly 26 characters.
	 * - First character must be 0-7 (to prevent overflow).
	 * - Uses Base32 (excludes I, L, O, U to avoid ambiguity and accidental profanity).
	 */
	private const string REGEX_PATTERN = '/^[0-7][0-9A-HJKMNP-TV-Z]{25}$/i';

	/**
	 * The native string value of the aggregate ID.
	 */
	private string $value;

	/**
	 * The constructor.
	 */
	final public function __construct(
		string $value
	) {
		if (!$this->isValid($value)) {
			$this->throwException($value);
		}

		$this->value = strtoupper($value);
	}

	/**
	 * String representation of the aggregate ID.
	 */
	public function __toString(): string
	{
		return $this->value;
	}

	/**
	 * Get the native string value of the aggregate ID.
	 */
	public function toNative(): string
	{
		return $this->value;
	}

	/**
	 * Equality check based on the value.
	 */
	public function equals(AggregateId $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Factory method to create a new instance from a string.
	 */
	public static function fromString(string $value): static
	{
		return new static($value);
	}

	/**
	 * Throws a domain-specific exception when validation fails.
	 */
	abstract protected function throwException(string $value): never;

	/**
	 * Validates if the value perfectly matches the ULID specification.
	 */
	private function isValid(string $value): bool
	{
		return preg_match(self::REGEX_PATTERN, $value) === 1;
	}
}
