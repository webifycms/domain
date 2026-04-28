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

namespace Webify\User\Authorization\Domain\Entity;

use Webify\Base\Domain\Entity\AggregateRoot;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authorization\Domain\Collection\PermissionCollection;
use Webify\User\Authorization\Domain\Event\{PermissionGranted, PermissionRevoked, RoleCreated};
use Webify\User\Authorization\Domain\ValueObject\{Permission, RoleId, RoleName, RoleSlug};

/**
 * Role aggregate root.
 *
 * Represent a named bundle of permissions that can be assigned to subjects.
 * The Role encapsulates the permission-matching logic so that no caller ever
 * iterates over permissions directly; they ask the role whether it permits something.
 * Roles support a built-in flag to mark system roles (e.g. Super Admin) as non-deletable by application code.
 */
final class Role extends AggregateRoot
{
	/**
	 * Private constructor enforces the use of the factory method.
	 */
	private function __construct(
		private readonly RoleId $id,
		private RoleName $name,
		private RoleSlug $slug,
		private PermissionCollection $permissions,
		private bool $isSystemRole
	) {}

	/**
	 * Get the ID.
	 */
	public function getId(): RoleId
	{
		return $this->id;
	}

	/**
	 * Get the name.
	 */
	public function getName(): RoleName
	{
		return $this->name;
	}

	/**
	 * Get the slug.
	 */
	public function getSlug(): RoleSlug
	{
		return $this->slug;
	}

	/**
	 * Get the permissions.
	 */
	public function getPermissions(): PermissionCollection
	{
		return $this->permissions;
	}

	/**
	 * Check if the role is a system role.
	 */
	public function isSystemRole(): bool
	{
		return $this->isSystemRole;
	}

	/**
	 * Grant permission to the role.
	 */
	public function grant(Permission $permission): void
	{
		$this->permissions = $this->permissions->with($permission);

		$this->recordDomainEvent(
			new PermissionGranted(
				$this->id->toNative(),
				$this->name->toNative(),
				$this->slug->toNative(),
				(string) $permission,
				DateTime::now()->toNative()
			)
		);
	}

	/**
	 * Revoke permission from the role.
	 */
	public function revoke(Permission $permission): void
	{
		$this->permissions = $this->permissions->without(
			fn (Permission $existingPermission): bool => $existingPermission->equals($permission)
		);

		$this->recordDomainEvent(
			new PermissionRevoked(
				$this->id->toNative(),
				$this->name->toNative(),
				$this->slug->toNative(),
				(string) $permission,
				DateTime::now()->toNative()
			)
		);
	}

	/**
	 * Check if the role allows a permission.
	 */
	public function allows(string $scope, string $action, string $resource): bool
	{
		foreach ($this->permissions as $permission) {
			if ($permission->matches($scope, $action, $resource)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Factory method to create a new role.
	 */
	public static function create(
		RoleId $id,
		RoleName $name,
		RoleSlug $slug,
		PermissionCollection $permissions,
		bool $isSystemRole = false
	): self {
		$role = new self($id, $name, $slug, $permissions, $isSystemRole);

		$role->recordDomainEvent(
			new RoleCreated(
				$role->getId()->toNative(),
				$role->getName()->toNative(),
				$role->getSlug()->toNative(),
				implode(
					';' . PHP_EOL,
					array_map(
						fn (Permission $permission): string => (string) $permission,
						$role->getPermissions()->toArray()
					)
				),
				$role->isSystemRole(),
				DateTime::now()->toNative()
			)
		);

		return $role;
	}
}
