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

namespace Webify\User\Authentication\Domain\ValueObject;

use Webify\User\Authentication\Domain\Exception\InvalidAccessTokenException;

/**
 * Access token value object.
 *
 * Represent an opaque, cryptographically random bearer token string handed to a client.
 * The value object wraps the raw token string and enforces a minimum entropy constraint
 * (length ≥ 64 characters) so weak tokens can never be persisted.
 */
final readonly class AccessToken
{
	/**
	 * Minimum length of the access token.
	 */
	private const int MIN_LENGTH = 64;

	/**
	 * Private constructor enforces the use of the factory method.
	 *
	 * @throws InvalidAccessTokenException if the provided value is invalid
	 */
	private function __construct(
		private string $value
	) {
		if (!$this->isValid()) {
			throw InvalidAccessTokenException::forInvalidAccessToken($this->value);
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
	 * Get the native string value of the access token.
	 */
	public function toNative(): string
	{
		return $this->value;
	}

	/**
	 * Equality check based on the value.
	 */
	public function equals(self $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Creates a new cryptographically random access token.
	 *
	 * Uses random_bytes() and bin2hex() to produce a 64-character hex token.
	 */
	public static function generate(): AccessToken
	{
		return new self(bin2hex(random_bytes(32)));
	}

	/**
	 * Factory method to create a new instance from a string.
	 */
	public static function fromString(string $value): AccessToken
	{
		return new self($value);
	}

	/**
	 * Checks if the current value meets the minimum length requirement.
	 *
	 * @return bool returns true if the value is valid, otherwise false
	 */
	private function isValid(): bool
	{
		if (strlen($this->value) < self::MIN_LENGTH) {
			return false;
		}

		return true;
	}
}
