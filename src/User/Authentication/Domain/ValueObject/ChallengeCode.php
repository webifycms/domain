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

use Random\RandomException;
use Webify\User\Authentication\Domain\Exception\{ChallengeCodeGenerationFailedException, InvalidChallengeCodeException};

/**
 * Challenge code value object.
 *
 * Represents a 6-digit numeric code used for authentication challenges.
 */
final readonly class ChallengeCode
{
	/**
	 * The length of the challenge code.
	 */
	private const int LENGTH = 6;

	/**
	 * Private constructor enforces the use of the factory methods.
	 * 1. Validates the provided value.
	 * 2. Throws an exception if the value is invalid.
	 */
	private function __construct(
		private string $value
	) {
		if (!$this->isValid()) {
			throw InvalidChallengeCodeException::forInvalidCode($this->value);
		}
	}

	/**
	 * Converts the object into its string representation.
	 */
	public function __toString(): string
	{
		return $this->value;
	}

	/**
	 * Creates an instance of the class from a native integer value.
	 */
	public static function fromNative(int $value): self
	{
		return new self(str_pad((string) $value, self::LENGTH, '0', STR_PAD_LEFT));
	}

	/**
	 * Creates an instance of the class from a string representation.
	 */
	public static function fromString(string $value): self
	{
		return new self($value);
	}

	/**
	 * Generates a new challenge code.
	 */
	public static function generate(): self
	{
		try {
			$code = str_pad(
				(string) random_int(0, 999999),
				self::LENGTH,
				'0',
				STR_PAD_LEFT
			);
		} catch (RandomException $exception) {
			throw ChallengeCodeGenerationFailedException::create($exception);
		}

		return new self($code);
	}

	/**
	 * Checks if the current challenge code is equal to another challenge code.
	 */
	public function equals(self $other): bool
	{
		return $this->value === $other->value;
	}

	/**
	 * Converts the object into its native string representation.
	 */
	public function toNative(): string
	{
		return $this->value;
	}

	/**
	 * Validates the challenge code.
	 */
	private function isValid(): bool
	{
		if (strlen($this->value) !== self::LENGTH) {
			return false;
		}

		return true;
	}
}
