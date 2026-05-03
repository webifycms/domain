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

namespace Webify\Test\User\Authorization\Domain\Service;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\{SlugifyInterface, UlidGeneratorInterface};
use Webify\User\Authorization\Domain\Entity\Role;
use Webify\User\Authorization\Domain\Exception\RoleAlreadyExistsException;
use Webify\User\Authorization\Domain\Guard\RoleAlreadyExists;
use Webify\User\Authorization\Domain\Repository\RoleRepositoryInterface;
use Webify\User\Authorization\Domain\Service\CreateRole;
use Webify\User\Authorization\Domain\ValueObject\{RoleName, RoleSlug};

/**
 * CreateRoleTest tests the functionality of the CreateRole service.
 *
 * @internal
 */
#[CoversClass(CreateRole::class)]
#[CoversMethod(CreateRole::class, 'create')]
final class CreateRoleTest extends TestCase
{
	/**
	 * Tests that a role can be created successfully with permissions.
	 */
	#[Test]
	public function testCreateRoleSuccessfullyWithPermissions(): void
	{
		$slugify         = $this->createStub(SlugifyInterface::class);
		$idGenerator     = $this->createStub(UlidGeneratorInterface::class);
		$repository      = $this->createMock(RoleRepositoryInterface::class);
		$eventPublisher  = $this->createMock(DomainEventPublisherInterface::class);

		$slugify->method('slugify')
			->willReturnCallback(fn (string $value): string => strtolower($value))
		;
		$idGenerator->method('generate')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FAV')
		;

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('isExist')
			->willReturn(false)
		;

		$roleAlreadyExists = new RoleAlreadyExists($roleRepository);

		$repository->expects($this->once())
			->method('persist')
		;
		$eventPublisher->expects($this->once())
			->method('publish')
		;

		$service = new CreateRole(
			$slugify,
			$idGenerator,
			$roleAlreadyExists,
			$repository,
			$eventPublisher
		);
		$role = $service->create(
			'Editor',
			[
				['scope' => 'post', 'action' => 'create', 'resource' => 'own'],
				['scope' => 'post', 'action' => 'update', 'resource' => 'own'],
			],
			'webify'
		);

		$this->assertInstanceOf(Role::class, $role);
		$this->assertEquals(RoleName::fromString('Editor'), $role->getName());
		$this->assertEquals(RoleSlug::fromString('webify.editor'), $role->getSlug());
		$this->assertCount(2, $role->getPermissions());
	}

	/**
	 * Tests that a role can be created successfully with empty permissions.
	 */
	#[Test]
	public function testCreateRoleSuccessfullyWithEmptyPermissions(): void
	{
		$slugify         = $this->createStub(SlugifyInterface::class);
		$idGenerator     = $this->createStub(UlidGeneratorInterface::class);
		$repository      = $this->createMock(RoleRepositoryInterface::class);
		$eventPublisher  = $this->createMock(DomainEventPublisherInterface::class);

		$slugify->method('slugify')
			->willReturnCallback(fn (string $value): string => strtolower($value))
		;
		$idGenerator->method('generate')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FBW')
		;

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('isExist')
			->willReturn(false)
		;

		$roleAlreadyExists = new RoleAlreadyExists($roleRepository);

		$repository->expects($this->once())
			->method('persist')
		;
		$eventPublisher->expects($this->once())
			->method('publish')
		;

		$service = new CreateRole(
			$slugify,
			$idGenerator,
			$roleAlreadyExists,
			$repository,
			$eventPublisher
		);
		$role = $service->create('Viewer', [], 'webify');

		$this->assertInstanceOf(Role::class, $role);
		$this->assertEquals(RoleName::fromString('Viewer'), $role->getName());
		$this->assertEquals(RoleSlug::fromString('webify.viewer'), $role->getSlug());
		$this->assertCount(0, $role->getPermissions());
	}

	/**
	 * Tests that creating a role with a duplicate slug throws an exception.
	 */
	#[Test]
	public function testCreateRoleWithDuplicateSlugThrowsException(): void
	{
		$slugify         = $this->createStub(SlugifyInterface::class);
		$idGenerator     = $this->createStub(UlidGeneratorInterface::class);
		$repository      = $this->createMock(RoleRepositoryInterface::class);
		$eventPublisher  = $this->createMock(DomainEventPublisherInterface::class);

		$slugify->method('slugify')
			->willReturnCallback(fn (string $value): string => strtolower($value))
		;

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('isExist')
			->willReturn(true)
		;

		$roleAlreadyExists = new RoleAlreadyExists($roleRepository);

		$repository->expects($this->never())
			->method('persist')
		;
		$eventPublisher->expects($this->never())
			->method('publish')
		;

		$service = new CreateRole(
			$slugify,
			$idGenerator,
			$roleAlreadyExists,
			$repository,
			$eventPublisher
		);

		$this->expectException(RoleAlreadyExistsException::class);
		$service->create('Admin', [], 'webify');
	}

	/**
	 * Tests that the slug is correctly derived from vendor and role name.
	 */
	#[Test]
	public function testSlugIsDerivedFromVendorAndRoleName(): void
	{
		$slugify         = $this->createStub(SlugifyInterface::class);
		$idGenerator     = $this->createStub(UlidGeneratorInterface::class);
		$repository      = $this->createStub(RoleRepositoryInterface::class);
		$eventPublisher  = $this->createStub(DomainEventPublisherInterface::class);

		$slugify->method('slugify')
			->willReturnCallback(
				fn (string $value): string => strtolower(
					trim((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $value), '-')
				)
			)
		;
		$idGenerator->method('generate')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FCX')
		;

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('isExist')
			->willReturn(false)
		;

		$roleAlreadyExists = new RoleAlreadyExists($roleRepository);
		$service           = new CreateRole(
			$slugify,
			$idGenerator,
			$roleAlreadyExists,
			$repository,
			$eventPublisher
		);
		$role = $service->create('Super Admin', [], 'my-vendor');

		$this->assertEquals(RoleSlug::fromString('my-vendor.super-admin'), $role->getSlug());
	}

	/**
	 * Tests that permissions are correctly converted to PermissionCollection.
	 */
	#[Test]
	public function testPermissionsAreCorrectlyConverted(): void
	{
		$slugify         = $this->createStub(SlugifyInterface::class);
		$idGenerator     = $this->createStub(UlidGeneratorInterface::class);
		$repository      = $this->createStub(RoleRepositoryInterface::class);
		$eventPublisher  = $this->createStub(DomainEventPublisherInterface::class);

		$slugify->method('slugify')
			->willReturnCallback(fn (string $value): string => strtolower($value))
		;
		$idGenerator->method('generate')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FDY')
		;

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('isExist')
			->willReturn(false)
		;

		$roleAlreadyExists = new RoleAlreadyExists($roleRepository);
		$service           = new CreateRole(
			$slugify,
			$idGenerator,
			$roleAlreadyExists,
			$repository,
			$eventPublisher
		);
		$permissions = [
			['scope' => 'user', 'action' => 'read', 'resource' => 'all'],
			['scope' => 'user', 'action' => 'write', 'resource' => 'own'],
			['scope' => 'settings', 'action' => 'manage', 'resource' => 'all'],
		];
		$role = $service->create('Manager', $permissions, 'webify');

		$this->assertCount(3, $role->getPermissions());

		$permissionArray = $role->getPermissions()->toArray();

		$this->assertEquals('user', $permissionArray[0]->toNative()['scope']);
		$this->assertEquals('read', $permissionArray[0]->toNative()['action']);
		$this->assertEquals('all', $permissionArray[0]->toNative()['resource']);
	}
}
