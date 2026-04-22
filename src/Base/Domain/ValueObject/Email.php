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
 * Email value object base class.
 */
abstract readonly class Email
{
	/**
	 * The max length of the email address is 254 characters (RFC 5321 limit).
	 */
	private const int MAX_LENGTH = 254;

	/**
	 * Closed constructor to enforce the use of the factory method.
	 */
	final private function __construct(private string $value)
	{
		if (!$this->isValid()) {
			$this->throwException($this->value);
		}

		// Additional domain-specific validation
		$this->validateDomainRules($this->value);
	}

	/**
	 * Convert the value object to a string.
	 */
	public function __toString(): string
	{
		return $this->value;
	}

	/**
	 * Factory method to create an email value object from a string.
	 */
	public static function fromString(string $value): static
	{
		return new static(mb_strtolower(trim($value)));
	}

	/**
	 * Get the native string value of the email address.
	 */
	public function toNative(): string
	{
		return $this->value;
	}

	/**
	 * Equality check based on the value.
	 */
	public function equals(Email $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Optional domain-specific validation can be applied in the implementing class.
	 * If the validation fails, it should throw an exception.
	 */
	abstract protected function validateDomainRules(string $value): void;

	/**
	 * Throws a domain-specific exception when validation fails.
	 */
	abstract protected function throwException(string $value): never;

	/**
	 * Validate the email address.
	 */
	private function isValid(): bool
	{
		if (empty($this->value)) {
			return false;
		}

		if (mb_strlen($this->value) > self::MAX_LENGTH) {
			return false;
		}

		if (filter_var($this->value, FILTER_VALIDATE_EMAIL) === false) {
			return false;
		}

		return true;
	}
}
