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

use Webify\User\Authorization\Domain\Exception\InvalidSubjectAttributesException;

/**
 * Subject attributes value object.
 */
final readonly class SubjectAttributes
{
	/**
	 * Private constructor enforces the use of the factory method.
	 *
	 * @param array<string, string>|array{} $values
	 */
	private function __construct(
		private array $values = []
	) {
		if (!$this->isValid()) {
			throw new InvalidSubjectAttributesException();
		}
	}

	/**
	 * Converts the object to its native representation.
	 *
	 * @return array<string, string>
	 */
	public function toNative(): array
	{
		return $this->values;
	}

	/**
	 * Gets the value of the attribute with the given key.
	 *
	 * @return array<string, string>|array{}
	 */
	public function get(string $key): array
	{
		if (isset($this->values[$key])) {
			return [$key => $this->values[$key]];
		}

		return [];
	}

	/**
	 * Factory method creates a new instance of the class from the attributes.
	 *
	 * @param array<string, string>|array{} $attributes an associative array of attributes with key, value pairs
	 *
	 * @return self a new instance of the class
	 */
	public static function fromArray(array $attributes = []): self
	{
		return new self($attributes);
	}

	/**
	 * Validates the attributes.
	 */
	private function isValid(): bool
	{
		if ([] === $this->values) {
			return true;
		}

		// @phpstan-ignore-next-line
		if (array_is_list($this->values)) {
			return false;
		}

		foreach ($this->values as $key => $value) {
			return match (true) {
				// @phpstan-ignore-next-line
				!is_string($key), !is_string($value) => false,
				default                              => true
			};
		}
	}
}
