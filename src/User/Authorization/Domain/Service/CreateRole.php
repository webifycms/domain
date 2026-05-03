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

namespace Webify\User\Authorization\Domain\Service;

use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\{SlugifyInterface, UlidGeneratorInterface};
use Webify\User\Authorization\Domain\Collection\PermissionCollection;
use Webify\User\Authorization\Domain\Entity\Role;
use Webify\User\Authorization\Domain\Guard\RoleAlreadyExists;
use Webify\User\Authorization\Domain\Repository\RoleRepositoryInterface;
use Webify\User\Authorization\Domain\ValueObject\{Permission, RoleId, RoleName, RoleSlug};

/**
 * CrateRole service handles the creation of a new role.
 *
 * Encapsulate the use case of defining a new role and its initial set of permissions.
 * The slug is derived automatically from the name if not provided explicitly, ensuring uniqueness and consistency.
 * This service is the entry point for both framework-defined default roles (seeded at installation time)
 * and extension-defined roles (registered during extension activation).
 *
 * Attempting to create a role with a duplicate slug must fail with a clear exception rather than silently overwriting.
 */
final readonly class CreateRole
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private SlugifyInterface $slugify,
		private UlidGeneratorInterface $idGenerator,
		private RoleAlreadyExists $roleAlreadyExists,
		private RoleRepositoryInterface $repository,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Creates a new role with the specified name and permissions.
	 *
	 * @param string $name the name of the role
	 * @param array<int, array{
	 *     scope: string,
	 *     action: string,
	 *     resource: string
	 * }> $permissions A list of permissions assigned to the role
	 * @param string $vendor the vendor use to prefx the role slug
	 *
	 * @return Role the newly created role instance
	 */
	public function create(string $name, array $permissions, string $vendor): Role
	{
		$roleName = RoleName::fromString($name);
		$fullSlug = $this->slugify->slugify($vendor) . '.' . $this->slugify->slugify($roleName->toNative());
		$roleSlug = RoleSlug::fromString($fullSlug);

		$this->roleAlreadyExists->guard($roleSlug);

		$role = Role::create(
			RoleId::fromString($this->idGenerator->generate()),
			$roleName,
			$roleSlug,
			$this->createPermissionCollection($permissions)
		);

		$this->repository->persist($role);
		$this->eventPublisher->publish(...$role->getDomainEvents());

		return $role;
	}

	/**
	 * Creates a PermissionCollection from an array of permissions.
	 *
	 * @param array<int, array{
	 *     scope: string,
	 *     action: string,
	 *     resource: string
	 * }> $permissions The array of permissions in their native form
	 */
	private function createPermissionCollection(array $permissions): PermissionCollection
	{
		$objects = [];

		foreach ($permissions as $permission) {
			$objects[] = Permission::fromNative($permission);
		}

		return PermissionCollection::from($objects);
	}
}
