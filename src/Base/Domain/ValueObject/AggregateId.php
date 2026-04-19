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
 */
abstract readonly class AggregateId
{
	/**
	 * ID specification regex pattern:
	 * - Must be exactly 26 characters.
	 * - First character must be 0-7 (to prevent overflow).
	 * - Uses Crockford's Base32 (excludes I, L, O, U to avoid ambiguity and accidental profanity).
	 */
	private const string REGEX_PATTERN = '/^[0-7][0-9A-HJKMNP-TV-Z]{25}$/i';

	/**
	 * The constructor.
	 */
	final public function __construct(
		private string $value
	) {
		if (!$this->isValid()) {
			$this->throwException(['value' => $this->value]);
		}
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
		return new static(strtoupper($value));
	}

	/**
	 * Throws a domain specific exception when validation fails.
	 *
	 * @param array<string, string> $params
	 */
	abstract public function throwException(array $params): void;

	/**
	 * Validates if the value perfectly matches the ULID specification.
	 */
	private function isValid(): bool
	{
		return preg_match(self::REGEX_PATTERN, $this->value) === 1;
	}
}
