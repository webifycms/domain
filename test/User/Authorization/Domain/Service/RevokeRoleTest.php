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
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authorization\Domain\Entity\RoleAssignment;
use Webify\User\Authorization\Domain\Event\RoleRevoked;
use Webify\User\Authorization\Domain\Exception\RoleAssignmentNotFoundException;
use Webify\User\Authorization\Domain\Repository\RoleAssignmentRepositoryInterface;
use Webify\User\Authorization\Domain\Service\RevokeRole;
use Webify\User\Authorization\Domain\ValueObject\{RoleAssignmentId, RoleId, SubjectId, TenantId};

/**
 * RevokeRoleTest tests the functionality of the RevokeRole service.
 *
 * @internal
 */
#[CoversClass(RevokeRole::class)]
#[CoversMethod(RevokeRole::class, 'revoke')]
final class RevokeRoleTest extends TestCase
{
	/**
	 * Identifier for the assignment.
	 */
	private RoleAssignmentId $assignmentId;

	/**
	 * Represents the identifier of a role.
	 */
	private RoleId $roleId;

	/**
	 * Denotes the identifier of a subject.
	 */
	private SubjectId $subjectId;

	/**
	 * Denotes the unique identifier of a tenant.
	 */
	private TenantId $tenantId;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->assignmentId = RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$this->roleId       = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FBW');
		$this->subjectId    = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FCX');
		$this->tenantId     = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FDY');
	}

	/**
	 * Tests that a role can be revoked successfully.
	 */
	#[Test]
	public function testRevokeRoleSuccessfully(): void
	{
		$assignment               = RoleAssignment::assign($this->assignmentId, $this->roleId, $this->subjectId);
		$roleAssignmentRepository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$roleAssignmentRepository->method('getById')
			->willReturn($assignment)
		;
		$roleAssignmentRepository->expects($this->once())
			->method('delete')
			->with($assignment)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->once())
			->method('publish')
			->with($this->isInstanceOf(RoleRevoked::class))
		;

		$service = new RevokeRole($roleAssignmentRepository, $eventPublisher);

		$service->revoke($this->assignmentId->toNative());
	}

	/**
	 * Tests that revoking a non-existent role assignment is idempotent.
	 */
	#[Test]
	public function testRevokeNonExistentRoleIsIdempotent(): void
	{
		$roleAssignmentRepository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$roleAssignmentRepository->method('getById')
			->willThrowException(new RoleAssignmentNotFoundException('Role assignment not found'))
		;
		$roleAssignmentRepository->expects($this->never())
			->method('delete')
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->never())
			->method('publish')
		;

		$service = new RevokeRole($roleAssignmentRepository, $eventPublisher);

		$service->revoke($this->assignmentId->toNative());
	}

	/**
	 * Tests that a role with tenant ID can be revoked successfully.
	 */
	#[Test]
	public function testRevokeRoleWithTenantId(): void
	{
		$assignment               = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			$this->tenantId
		);
		$roleAssignmentRepository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$roleAssignmentRepository->method('getById')
			->willReturn($assignment)
		;
		$roleAssignmentRepository->expects($this->once())
			->method('delete')
			->with($assignment)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->once())
			->method('publish')
			->with($this->isInstanceOf(RoleRevoked::class))
		;

		$service = new RevokeRole($roleAssignmentRepository, $eventPublisher);

		$service->revoke($this->assignmentId->toNative());
	}

	/**
	 * Tests that a role with an expiration date can be revoked successfully.
	 */
	#[Test]
	public function testRevokeRoleWithExpiresAt(): void
	{
		$expiresAt  = DateTime::fromTimestamp(time() + 3600);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			null,
			$expiresAt
		);
		$roleAssignmentRepository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$roleAssignmentRepository->method('getById')
			->willReturn($assignment)
		;

		$roleAssignmentRepository->expects($this->once())
			->method('delete')
			->with($assignment)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->once())
			->method('publish')
			->with($this->isInstanceOf(RoleRevoked::class))
		;

		$service = new RevokeRole($roleAssignmentRepository, $eventPublisher);

		$service->revoke($this->assignmentId->toNative());
	}
}
