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

use Webify\User\Authorization\Domain\Exception\InvalidRoleSlugException;

/**
 * Role slug value object.
 */
final readonly class RoleSlug
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
	final public function __construct(
		private string $vendor,
		private string $slug
	) {
		if (!$this->isValid()) {
			throw InvalidRoleSlugException::forInvalidSlug($this->slug);
		}
	}

	/**
	 * Converts the object to its string representation.
	 */
	public function __toString(): string
	{
		return $this->vendor . '.' . $this->slug;
	}

	/**
	 * Factory method to create a slug value object from a string.
	 *
	 * @throws InvalidRoleSlugException if the slug did not have the dot separator
	 */
	public static function fromString(string $value): self
	{
		if (false === str_contains($value, '.')) {
			throw InvalidRoleSlugException::forInvalidSeparator($value);
		}

		[$vendor, $slug] = explode('.', $value);

		return new self($vendor, $slug);
	}

	/**
	 * Returns the vendor part of the slug.
	 */
	public function getVendor(): string
	{
		return $this->vendor;
	}

	/**
	 * Returns the slug part of the slug.
	 */
	public function getSlug(): string
	{
		return $this->slug;
	}

	/**
	 * Converts the object to its native string representation.
	 *
	 * @return array{vendor: string, slug: string}
	 */
	public function toNative(): array
	{
		return [
			'vendor' => $this->vendor,
			'slug'   => $this->slug,
		];
	}

	/**
	 * Compares the current object with another instance for equality.
	 *
	 * @param RoleSlug $other the instance to compare with the current object
	 *
	 * @return bool true if both instances are considered equal, false otherwise
	 */
	public function equals(self $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Validates the value against a predefined format pattern.
	 */
	private function isValid(): bool
	{
		if (false === (bool) preg_match(self::PATTERN_FORMAT, $this->vendor)) {
			return false;
		}

		return (bool) preg_match(self::PATTERN_FORMAT, $this->slug);
	}
}
