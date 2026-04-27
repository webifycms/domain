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

namespace Webify\Test\User\Authorization\Domain\Entity;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authorization\Domain\Collection\PermissionCollection;
use Webify\User\Authorization\Domain\Entity\Role;
use Webify\User\Authorization\Domain\Event\{PermissionGranted, PermissionRevoked, RoleCreated};
use Webify\User\Authorization\Domain\ValueObject\{Permission, RoleId, RoleName, RoleSlug};

/**
 * RoleTest tests the functionality of the Role entity.
 *
 * @internal
 */
#[CoversClass(Role::class)]
#[CoversMethod(Role::class, 'getId')]
#[CoversMethod(Role::class, 'getName')]
#[CoversMethod(Role::class, 'getSlug')]
#[CoversMethod(Role::class, 'getPermissions')]
#[CoversMethod(Role::class, 'isSystemRole')]
#[CoversMethod(Role::class, 'grant')]
#[CoversMethod(Role::class, 'revoke')]
#[CoversMethod(Role::class, 'allows')]
#[CoversMethod(Role::class, 'create')]
final class RoleTest extends TestCase
{
	/**
	 * Role instance.
	 */
	private Role $role;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->role = Role::create(
			RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			RoleName::fromString('Admin'),
			RoleSlug::fromString('admin'),
			new PermissionCollection()
		);
	}

	/**
	 * Tests role-created event.
	 */
	#[Test]
	public function testRoleCreatedEvent(): void
	{
		$events = $this->role->getDomainEvents();

		$this->assertCount(1, $events);
		$this->assertInstanceOf(RoleCreated::class, $events[0]);
	}

	/**
	 * Tests role getters return correct values.
	 */
	#[Test]
	public function testRoleGetters(): void
	{
		$this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', $this->role->getId()->toNative());
		$this->assertSame('Admin', $this->role->getName()->toNative());
		$this->assertSame('admin', $this->role->getSlug()->toNative());
		$this->assertFalse($this->role->isSystemRole());
		$this->assertCount(0, $this->role->getPermissions());
	}

	/**
	 * Tests system role flag.
	 */
	#[Test]
	public function testSystemRoleFlag(): void
	{
		$systemRole = Role::create(
			RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			RoleName::fromString('Super Admin'),
			RoleSlug::fromString('super-admin'),
			new PermissionCollection(),
			true
		);

		$this->assertTrue($systemRole->isSystemRole());
	}

	/**
	 * Tests granting permission to a role.
	 */
	#[Test]
	public function testGrantPermission(): void
	{
		$permission = Permission::fromNative([
			'scope'    => 'post',
			'action'   => 'create',
			'resource' => 'article',
		]);

		$this->role->grant($permission);

		$this->assertCount(1, $this->role->getPermissions());
		$this->assertTrue($this->role->allows('post', 'create', 'article'));

		$events = $this->role->getDomainEvents();

		$this->assertCount(2, $events);
		$this->assertInstanceOf(PermissionGranted::class, $events[1]);
	}

	/**
	 * Tests revoking permission from a role.
	 */
	#[Test]
	public function testRevokePermission(): void
	{
		$permission = Permission::fromNative([
			'scope'    => 'post',
			'action'   => 'create',
			'resource' => 'article',
		]);

		$this->role->grant($permission);
		$this->role->revoke($permission);

		$this->assertCount(0, $this->role->getPermissions());
		$this->assertFalse($this->role->allows('post', 'create', 'article'));

		$events = $this->role->getDomainEvents();

		$this->assertCount(3, $events);
		$this->assertInstanceOf(PermissionRevoked::class, $events[2]);
	}

	/**
	 * Tests allows method returns true for matching permission.
	 */
	#[Test]
	public function testAllowsReturnsTrueForMatchingPermission(): void
	{
		$permission = Permission::fromNative([
			'scope'    => 'user',
			'action'   => 'read',
			'resource' => 'profile',
		]);

		$this->role->grant($permission);

		$this->assertTrue($this->role->allows('user', 'read', 'profile'));
	}

	/**
	 * Tests allows method returns false for non-matching permission.
	 */
	#[Test]
	public function testAllowsReturnsFalseForNonMatchingPermission(): void
	{
		$permission = Permission::fromNative([
			'scope'    => 'user',
			'action'   => 'read',
			'resource' => 'profile',
		]);

		$this->role->grant($permission);

		$this->assertFalse($this->role->allows('user', 'delete', 'profile'));
	}
}
