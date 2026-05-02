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

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\User\Authorization\Domain\Collection\PermissionCollection;
use Webify\User\Authorization\Domain\Entity\Role;
use Webify\User\Authorization\Domain\Exception\DuplicateRoleAssignmentException;
use Webify\User\Authorization\Domain\Guard\DuplicateRoleAssignment;
use Webify\User\Authorization\Domain\Repository\{RoleAssignmentRepositoryInterface, RoleRepositoryInterface};
use Webify\User\Authorization\Domain\Service\AssignRole;
use Webify\User\Authorization\Domain\ValueObject\{RoleId, RoleName, RoleSlug, SubjectId, TenantId};

/**
 * RoleAssignTest tests the functionality of the RoleAssign service.
 *
 * @internal
 */
#[CoversClass(AssignRole::class)]
#[CoversMethod(AssignRole::class, 'assign')]
final class AssignRoleTest extends TestCase
{
	/**
	 * Identifier for the role.
	 */
	private RoleId $roleId;

	/**
	 * Identifier for the subject.
	 */
	private SubjectId $subjectId;

	/**
	 * Identifier for the tenant.
	 */
	private TenantId $tenantId;

	/**
	 * Slug for the role.
	 */
	private RoleSlug $roleSlug;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->roleId    = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$this->subjectId = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FBW');
		$this->tenantId  = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FCX');
		$this->roleSlug  = RoleSlug::fromString('admin');
	}

	/**
	 * Tests that a role can be assigned successfully.
	 */
	#[Test]
	public function testAssignRoleSuccessfully(): void
	{
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Admin'),
			$this->roleSlug,
			new PermissionCollection()
		);
		$roleRepository = $this->createMock(RoleRepositoryInterface::class);

		$roleRepository->method('getBySlug')
			->willReturn($role)
		;

		$roleAssignmentRepository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$roleAssignmentRepository->expects($this->once())
			->method('persist')
		;

		$idGenerator = $this->createMock(UlidGeneratorInterface::class);

		$idGenerator->method('generate')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FDY')
		;

		$duplicateRoleAssignmentRepo = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$duplicateRoleAssignmentRepo->method('isExists')
			->willReturn(false)
		;

		$duplicateRoleAssignment = new DuplicateRoleAssignment($duplicateRoleAssignmentRepo);
		$eventPublisher          = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->once())
			->method('publish')
		;

		$service = new AssignRole(
			$roleRepository,
			$roleAssignmentRepository,
			$idGenerator,
			$duplicateRoleAssignment,
			$eventPublisher
		);

		$service->assign($this->subjectId->toNative(), $this->roleSlug->toNative());
	}

	/**
	 * Tests that a role can be assigned with a tenant ID.
	 */
	#[Test]
	public function testAssignRoleWithTenantId(): void
	{
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Admin'),
			$this->roleSlug,
			new PermissionCollection()
		);
		$roleRepository = $this->createMock(RoleRepositoryInterface::class);

		$roleRepository->method('getBySlug')
			->willReturn($role)
		;

		$roleAssignmentRepository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$roleAssignmentRepository->expects($this->once())
			->method('persist')
		;

		$idGenerator = $this->createMock(UlidGeneratorInterface::class);

		$idGenerator->method('generate')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FDY')
		;

		$duplicateRoleAssignmentRepo = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$duplicateRoleAssignmentRepo->method('isExists')
			->willReturn(false)
		;

		$duplicateRoleAssignment = new DuplicateRoleAssignment($duplicateRoleAssignmentRepo);
		$eventPublisher          = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->once())
			->method('publish')
		;

		$service = new AssignRole(
			$roleRepository,
			$roleAssignmentRepository,
			$idGenerator,
			$duplicateRoleAssignment,
			$eventPublisher
		);

		$service->assign(
			$this->subjectId->toNative(),
			$this->roleSlug->toNative(),
			$this->tenantId->toNative()
		);
	}

	/**
	 * Tests that a role can be assigned with an expiration date.
	 */
	#[Test]
	public function testAssignRoleWithExpiresAt(): void
	{
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Admin'),
			$this->roleSlug,
			new PermissionCollection()
		);
		$roleRepository = $this->createMock(RoleRepositoryInterface::class);

		$roleRepository->method('getBySlug')
			->willReturn($role)
		;

		$roleAssignmentRepository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$roleAssignmentRepository->expects($this->once())
			->method('persist')
		;

		$idGenerator = $this->createMock(UlidGeneratorInterface::class);

		$idGenerator->method('generate')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FDY')
		;

		$duplicateRoleAssignmentRepo = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$duplicateRoleAssignmentRepo->method('isExists')
			->willReturn(false)
		;

		$duplicateRoleAssignment = new DuplicateRoleAssignment($duplicateRoleAssignmentRepo);
		$eventPublisher          = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->once())
			->method('publish')
		;

		$service = new AssignRole(
			$roleRepository,
			$roleAssignmentRepository,
			$idGenerator,
			$duplicateRoleAssignment,
			$eventPublisher
		);

		$expireAt = new DateTimeImmutable('+1 hour');

		$service->assign(
			$this->subjectId->toNative(),
			$this->roleSlug->toNative(),
			null,
			$expireAt
		);
	}

	/**
	 * Tests that duplicate role assigning throws an exception.
	 */
	#[Test]
	public function testDuplicateRoleAssignmentThrowsException(): void
	{
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Admin'),
			$this->roleSlug,
			new PermissionCollection()
		);
		$roleRepository = $this->createMock(RoleRepositoryInterface::class);

		$roleRepository->method('getBySlug')
			->willReturn($role)
		;

		$duplicateRoleAssignmentRepo = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$duplicateRoleAssignmentRepo->method('isExists')
			->willReturn(true)
		;

		$duplicateRoleAssignment  = new DuplicateRoleAssignment($duplicateRoleAssignmentRepo);
		$roleAssignmentRepository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$roleAssignmentRepository->expects($this->never())
			->method('persist')
		;

		$idGenerator    = $this->createMock(UlidGeneratorInterface::class);
		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->never())
			->method('publish')
		;

		$service = new AssignRole(
			$roleRepository,
			$roleAssignmentRepository,
			$idGenerator,
			$duplicateRoleAssignment,
			$eventPublisher
		);

		$this->expectException(DuplicateRoleAssignmentException::class);
		$service->assign($this->subjectId->toNative(), $this->roleSlug->toNative());
	}
}
