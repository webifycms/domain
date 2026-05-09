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

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authorization\Domain\Entity\RoleAssignment;
use Webify\User\Authorization\Domain\Event\RoleAssigned;
use Webify\User\Authorization\Domain\ReadModel\RoleAssignment as RoleAssignmentReadModel;
use Webify\User\Authorization\Domain\ValueObject\{RoleAssignmentId, RoleId, SubjectId, TenantId};

/**
 * RoleAssignmentTest tests the functionality of the RoleAssignment aggregate root.
 *
 * @internal
 */
#[CoversClass(RoleAssignment::class)]
#[CoversMethod(RoleAssignment::class, 'assign')]
#[CoversMethod(RoleAssignment::class, 'getId')]
#[CoversMethod(RoleAssignment::class, 'getRoleId')]
#[CoversMethod(RoleAssignment::class, 'getSubjectId')]
#[CoversMethod(RoleAssignment::class, 'getTenantId')]
#[CoversMethod(RoleAssignment::class, 'getExpiresAt')]
#[CoversMethod(RoleAssignment::class, 'isApplicableFor')]
#[CoversMethod(RoleAssignment::class, 'isExpired')]
#[CoversMethod(RoleAssignment::class, 'reconstitute')]
final class RoleAssignmentTest extends TestCase
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
	 * Tests that the role assignment is correctly created with the given identifiers.
	 */
	#[Test]
	public function testAssignCreatesRoleAssignment(): void
	{
		$assignment = RoleAssignment::assign($this->assignmentId, $this->roleId, $this->subjectId);

		$this->assertInstanceOf(RoleAssignment::class, $assignment);
		$this->assertTrue($this->assignmentId->equals($assignment->getId()));
		$this->assertTrue($this->roleId->equals($assignment->getRoleId()));
		$this->assertTrue($this->subjectId->equals($assignment->getSubjectId()));
		$this->assertNull($assignment->getTenantId());
	}

	/**
	 * Tests the assignment of a role with a tenant ID to ensure the tenant ID is correctly
	 * associated with the created role assignment.
	 */
	#[Test]
	public function testAssignWithTenantId(): void
	{
		$assignment = RoleAssignment::assign($this->assignmentId, $this->roleId, $this->subjectId, $this->tenantId);
		/** @var TenantId $tenantId */
		$tenantId = $assignment->getTenantId();

		$this->assertTrue($this->tenantId->equals($tenantId));
	}

	/**
	 * Tests that the isExpired method returns false when no expiration is set on the role assignment.
	 */
	#[Test]
	public function testIsExpiredReturnsFalseWhenNoExpiration(): void
	{
		$assignment = RoleAssignment::assign($this->assignmentId, $this->roleId, $this->subjectId);

		$this->assertFalse($assignment->isExpired());
	}

	/**
	 * Tests that a role assignment with an expiration time is correctly created and the expiration time is set.
	 */
	#[Test]
	public function testAssignWithExpiresAt(): void
	{
		$expiresAt  = DateTime::fromTimestamp(time() + 3600);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			null,
			$expiresAt
		);

		$this->assertNotNull($assignment->getExpiresAt());
	}

	/**
	 * Tests that the isExpiresAt method returns true when the role assignment has expired.
	 */
	#[Test]
	public function testIsExpiresAtReturnTrueForExpired(): void
	{
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			$this->tenantId,
			DateTime::fromTimestamp(time() - 3600)
		);

		$this->assertTrue($assignment->isExpired());
	}

	/**
	 * Tests that the isExpiresAt method returns false when the role assignment has not expired.
	 */
	#[Test]
	public function testIsExpiresAtReturnFalseForNotExpired(): void
	{
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			$this->tenantId,
			DateTime::fromTimestamp(time() + 3600)
		);

		$this->assertFalse($assignment->isExpired());
	}

	/**
	 * Tests the applicability of a role assignment for a global context.
	 */
	#[Test]
	public function testIsApplicableForGlobalAssignment(): void
	{
		$assignment = RoleAssignment::assign($this->assignmentId, $this->roleId, $this->subjectId);

		$this->assertTrue($assignment->isApplicableFor(null));
		$this->assertTrue($assignment->isApplicableFor($this->tenantId));
	}

	/**
	 * Tests if a tenant-scoped role assignment is applicable to the given tenant.
	 */
	#[Test]
	public function testIsApplicableForTenantScopedAssignment(): void
	{
		$assignment = RoleAssignment::assign($this->assignmentId, $this->roleId, $this->subjectId, $this->tenantId);

		$this->assertFalse($assignment->isApplicableFor(null));
		$this->assertTrue($assignment->isApplicableFor($this->tenantId));
	}

	/**
	 * Tests whether a role assignment is not applicable for a different tenant.
	 */
	#[Test]
	public function testIsApplicableForDifferentTenant(): void
	{
		$otherTenant = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FEZ');
		$assignment  = RoleAssignment::assign($this->assignmentId, $this->roleId, $this->subjectId, $this->tenantId);

		$this->assertFalse($assignment->isApplicableFor($otherTenant));
	}

	/**
	 * Tests that assigning a role generates a domain event.
	 *
	 * This method verifies that when a role assignment is created, a
	 * corresponding domain event is recorded. It checks the number of
	 * generated events and ensures the correct event type is produced.
	 */
	#[Test]
	public function testAssignRecordsDomainEvent(): void
	{
		$assignment = RoleAssignment::assign($this->assignmentId, $this->roleId, $this->subjectId, $this->tenantId);
		$events = $assignment->getDomainEvents();

		$this->assertCount(1, $events);
		$this->assertInstanceOf(RoleAssigned::class, $events[0]);
	}

	/**
	 * Tests reconstitute restores a RoleAssignment without tenant or expiry.
	 */
	#[Test]
	public function testReconstituteWithoutTenantOrExpiry(): void
	{
		$readModel = new RoleAssignmentReadModel(
			id: '01ARZ3NDEKTSV4RRFFQ69G5FAV',
			roleId: '01ARZ3NDEKTSV4RRFFQ69G5FBW',
			subjectId: '01ARZ3NDEKTSV4RRFFQ69G5FCX'
		);
		$assignment = RoleAssignment::reconstitute($readModel);

		$this->assertTrue($this->assignmentId->equals($assignment->getId()));
		$this->assertTrue($this->roleId->equals($assignment->getRoleId()));
		$this->assertTrue($this->subjectId->equals($assignment->getSubjectId()));
		$this->assertNull($assignment->getTenantId());
		$this->assertNull($assignment->getExpiresAt());
		$this->assertFalse($assignment->isExpired());
		$this->assertTrue($assignment->isApplicableFor(null));
		$this->assertCount(0, $assignment->getDomainEvents());
	}

	/**
	 * Tests reconstitute restores a RoleAssignment with a tenant but without expiry.
	 */
	#[Test]
	public function testReconstituteWithTenantWithoutExpiry(): void
	{
		$readModel = new RoleAssignmentReadModel(
			id: '01ARZ3NDEKTSV4RRFFQ69G5FAV',
			roleId: '01ARZ3NDEKTSV4RRFFQ69G5FBW',
			subjectId: '01ARZ3NDEKTSV4RRFFQ69G5FCX',
			tenantId: '01ARZ3NDEKTSV4RRFFQ69G5FDY'
		);
		$assignment = RoleAssignment::reconstitute($readModel);

		$this->assertTrue($this->assignmentId->equals($assignment->getId()));
		$this->assertTrue($this->roleId->equals($assignment->getRoleId()));
		$this->assertTrue($this->subjectId->equals($assignment->getSubjectId()));
		$this->assertNotNull($assignment->getTenantId());
		$this->assertTrue($this->tenantId->equals($assignment->getTenantId()));
		$this->assertNull($assignment->getExpiresAt());
		$this->assertFalse($assignment->isExpired());
		$this->assertTrue($assignment->isApplicableFor($this->tenantId));
		$this->assertFalse($assignment->isApplicableFor(null));
		$this->assertCount(0, $assignment->getDomainEvents());
	}

	/**
	 * Tests reconstitute restores a RoleAssignment with tenant and expiry.
	 */
	#[Test]
	public function testReconstituteWithTenantAndExpiry(): void
	{
		$expiresAt = new DateTimeImmutable('+1 hour');
		$readModel = new RoleAssignmentReadModel(
			id: '01ARZ3NDEKTSV4RRFFQ69G5FAV',
			roleId: '01ARZ3NDEKTSV4RRFFQ69G5FBW',
			subjectId: '01ARZ3NDEKTSV4RRFFQ69G5FCX',
			tenantId: '01ARZ3NDEKTSV4RRFFQ69G5FDY',
			expiresAt: $expiresAt
		);
		$assignment = RoleAssignment::reconstitute($readModel);
		/** @var TenantId $tenantId */
		$tenantId = $assignment->getTenantId();

		$this->assertTrue($this->assignmentId->equals($assignment->getId()));
		$this->assertTrue($this->roleId->equals($assignment->getRoleId()));
		$this->assertTrue($this->subjectId->equals($assignment->getSubjectId()));
		$this->assertTrue($this->tenantId->equals($tenantId));
		$this->assertNotNull($assignment->getExpiresAt());
		$this->assertFalse($assignment->isExpired());
		$this->assertTrue($assignment->isApplicableFor($this->tenantId));
		$this->assertCount(0, $assignment->getDomainEvents());
	}

	/**
	 * Tests reconstitute restores a RoleAssignment with an expired date.
	 */
	#[Test]
	public function testReconstituteWithExpiredDate(): void
	{
		$expiresAt = new DateTimeImmutable('-1 hour');
		$readModel = new RoleAssignmentReadModel(
			id: '01ARZ3NDEKTSV4RRFFQ69G5FAV',
			roleId: '01ARZ3NDEKTSV4RRFFQ69G5FBW',
			subjectId: '01ARZ3NDEKTSV4RRFFQ69G5FCX',
			tenantId: '01ARZ3NDEKTSV4RRFFQ69G5FDY',
			expiresAt: $expiresAt
		);
		$assignment = RoleAssignment::reconstitute($readModel);

		$this->assertNotNull($assignment->getExpiresAt());
		$this->assertTrue($assignment->isExpired());
		$this->assertCount(0, $assignment->getDomainEvents());
	}
}
