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

use Webify\User\Authorization\Domain\Exception\InvalidPermissionException;

/**
 * Permission value object - Represent the atomic unit of access
 * — "action X is allowed on resource type Y". A Permission is immutable and has no identity of its own;
 * two permissions with the same action and resource are equal.
 * It supports a wildcard character (*) on both fields so that roles like Super Admin
 * can be expressed as a single Permission("*", "*") rather than an exhaustive matrix.
 * The wildcard logic must live inside this value object, so it is never reimplemented across the codebase.
 */
final readonly class Permission
{
	/**
	 * Constructs a new instance of the class.
	 *
	 * @param string $scope    the scope of the permission, e.g., "post", "user"
	 * @param string $action   the action to be performed
	 * @param string $resource the resource associated with the action
	 *
	 * @throws InvalidPermissionException if the one of the property is empty
	 */
	private function __construct(
		private string $scope,
		private string $action,
		private string $resource,
	) {
		if (!$this->isValid()) {
			throw new InvalidPermissionException();
		}
	}

	/**
	 * Returns a string representation of the Permission object.
	 */
	public function __toString(): string
	{
		return 'scope: ' . $this->scope
			. ', action: ' . $this->action
			. ', resource: ' . $this->resource;
	}

	/**
	 * Checks if the permission matches the provided scope, action, and resource.
	 * A permission matches if the scope, action, and resource are equal to the provided values,
	 * or if the provided values are wildcards.
	 */
	public function matches(string $scope, string $action, string $resource): bool
	{
		$match = '*' === $this->scope || $this->scope === $scope;
		$match = $match && ('*' === $this->action || $this->action === $action);

		return $match && ('*' === $this->resource || $this->resource === $resource);
	}

	/**
	 * Converts the current object state into a native array representation.
	 *
	 * @return array<string, string> an associative array containing the action and resource
	 */
	public function toNative(): array
	{
		return [
			'scope'    => $this->scope,
			'action'   => $this->action,
			'resource' => $this->resource,
		];
	}

	/**
	 * Checks if the current object is equal to another Permission object.
	 */
	public function equals(self $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Creates a new instance of the class from a native array structure.
	 *
	 * @param array{
	 *     scope: string,
	 *     action: string,
	 *     resource: string,
	 * } $permission an associative array containing 'action' and 'resource' keys
	 *
	 * @return self a new instance of the class initialized with the provided data
	 */
	public static function fromNative(array $permission): self
	{
		return new self($permission['scope'], $permission['action'], $permission['resource']);
	}

	/**
	 * Creates a new instance with wildcard values.
	 *
	 * @return self a new instance with wildcard configuration
	 */
	public static function wildcard(): self
	{
		return new self('*', '*', '*');
	}

	/**
	 * Creates a new instance with wildcard values for a specific scope.
	 *
	 * @param string $scope the scope for which to create the wildcard permission
	 *
	 * @return self a new instance with wildcard configuration for the specified scope
	 */
	public static function scopedWildcard(string $scope): self
	{
		return new self($scope, '*', '*');
	}

	/**
	 * Checks whether the action and resource are valid.
	 *
	 * @return bool true if both action and resource are non-empty, false otherwise
	 */
	private function isValid(): bool
	{
		if ('' === trim($this->scope) || '' === trim($this->action) || '' === trim($this->resource)) {
			return false;
		}

		return true;
	}
}
