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
use Webify\Base\Domain\Contract\Authorization\{AuthorizableResourceInterface, AuthorizableSubjectInterface, AuthorizationRuleInterface};
use Webify\Base\Domain\Service\Authorization\AuthorizationRuleRegistryInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authorization\Domain\Collection\{PermissionCollection, RoleAssignmentCollection};
use Webify\User\Authorization\Domain\Entity\{Role, RoleAssignment};
use Webify\User\Authorization\Domain\Exception\RoleNotFoundException;
use Webify\User\Authorization\Domain\Repository\{RoleAssignmentRepositoryInterface, RoleRepositoryInterface};
use Webify\User\Authorization\Domain\Service\Authorization;
use Webify\User\Authorization\Domain\ValueObject\{Permission, RoleAssignmentId, RoleId, RoleName, RoleSlug, SubjectId, TenantId};

/**
 * AuthorizationTest tests the functionality of the Authorization service.
 *
 * @internal
 */
#[CoversClass(Authorization::class)]
#[CoversMethod(Authorization::class, '__construct')]
#[CoversMethod(Authorization::class, 'check')]
#[CoversMethod(Authorization::class, 'evaluate')]
#[CoversMethod(Authorization::class, 'getTenantId')]
#[CoversMethod(Authorization::class, 'generateCacheKey')]
final class AuthorizationTest extends TestCase
{
	/**
	 * The identifier for a specific role.
	 */
	private RoleId $roleId;

	/**
	 * The unique identifier for a specific subject.
	 */
	private SubjectId $subjectId;

	/**
	 * The unique identifier for a specific tenant.
	 */
	private TenantId $tenantId;

	/**
	 * The unique identifier for a specific role assignment.
	 */
	private RoleAssignmentId $assignmentId;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->roleId       = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$this->subjectId    = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FBW');
		$this->tenantId     = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FCX');
		$this->assignmentId = RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FDY');
	}

	/**
	 * Tests whether the `check` method of the Authorization service returns `true` when the subject
	 * has a role with matching permissions to the given action and resource.
	 */
	#[Test]
	public function testCheckReturnsTrueWhenSubjectHasMatchingRoleAndPermission(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId
		);

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertTrue($service->check('read', $subject, $resource));
	}

	/**
	 * Tests that the `check` method of the `Authorization` service returns `false`
	 * when the subject has no role assignments.
	 */
	#[Test]
	public function testCheckReturnsFalseWhenSubjectHasNoAssignments(): void
	{
		$roleRepository       = $this->createStub(RoleRepositoryInterface::class);
		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertFalse($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `false`
	 * when the role assigned to the subject does not have a permission matching the
	 * required action, scope, and resource characteristics.
	 *
	 * The test ensures that when a role's permission collection lacks a specific
	 * permission for the requested operation (e.g., 'delete'), the service correctly
	 * denies the access check.
	 */
	#[Test]
	public function testCheckReturnsFalseWhenRoleDoesNotHaveMatchingPermission(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'write',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Viewer'),
			RoleSlug::fromString('webify.viewer'),
			$permissions,
			false
		);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId
		);

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertFalse($service->check('delete', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `false`
	 * when the role associated with the subject's role assignment cannot be found.
	 *
	 * This test ensures that if a role query fails to retrieve a role for an
	 * assignment, the service correctly denies the access check.
	 */
	#[Test]
	public function testCheckReturnsFalseWhenRoleNotFound(): void
	{
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId
		);
		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willThrowException(RoleNotFoundException::forId($this->roleId->toNative()))
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertFalse($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `false`
	 * when the role assignment for the subject has expired.
	 *
	 * The test ensures that if the role assignment's expiration date is in the past,
	 * the service correctly denies the access check, regardless of permissions
	 * associated with the role.
	 */
	#[Test]
	public function testCheckReturnsFalseWhenAssignmentIsExpired(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$expiredDate         = DateTime::fromString('-1 day');
		$assignment          = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			null,
			$expiredDate
		);

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertFalse($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `false`
	 * when the tenant ID associated with the subject does not match the tenant ID
	 * assigned to the role in the role assignment.
	 *
	 * The test ensures that a mismatch in tenant IDs results in the access check
	 * being denied, even if the role's permissions would otherwise allow the action
	 * to be performed.
	 */
	#[Test]
	public function testCheckReturnsFalseWhenTenantDoesNotMatch(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$differentTenant     = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FEZ');
		$assignment          = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			$differentTenant
		);

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn($this->tenantId->toNative())
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertFalse($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `true`
	 * when a globally scoped role assigned to a tenant-scoped subject contains a permission
	 * that matches the required action, scope, and resource characteristics.
	 *
	 * This test ensures that global roles with appropriate permissions correctly grant
	 * access to tenant-scoped subjects when the requested operation (e.g., 'read')
	 * aligns with the permissions defined in the role.
	 */
	#[Test]
	public function testCheckReturnsTrueWhenGlobalRoleAppliesToTenantScopedSubject(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId
		);

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn($this->tenantId->toNative())
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertTrue($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `false`
	 * when a role with scoped permissions is applied to a global context that does
	 * not satisfy the scope constraint of the permission.
	 *
	 * The test ensures that even if a role has a permission for the specified action,
	 * resource, and scope, the service denies the access check when the context in
	 * which the role is applied does not match the defined scope of the permission.
	 */
	#[Test]
	public function testCheckReturnsFalseWhenScopedRoleAppliedToGlobalContext(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			$this->tenantId
		);

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertFalse($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `true`
	 * when the role assigned to the subject has a permission matching the required
	 * action, scope, and resource characteristics, and the tenant associated with
	 * the assignment matches the tenant of the subject.
	 *
	 * The test ensures that when a role's permission collection and tenant context
	 * align with the requested operation (e.g., 'read'), the service correctly allows
	 * the access check.
	 */
	#[Test]
	public function testCheckReturnsTrueWhenTenantMatches(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			$this->tenantId
		);

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn($this->tenantId->toNative())
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertTrue($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `false`
	 * when the applicable authorization rule is not satisfied for the given action,
	 * subject, and resource.
	 *
	 * The test ensures that even if a role's permissions match the requested action,
	 * scope, and resource, the service correctly denies access if an applicable rule
	 * explicitly fails the satisfaction check.
	 */
	#[Test]
	public function testCheckReturnsFalseWhenRuleIsNotSatisfied(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId
		);

		$rule = $this->createStub(AuthorizationRuleInterface::class);

		$rule->method('isSatisfied')
			->willReturn(false)
		;

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([$rule])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertFalse($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `true`
	 * when all applicable rules are satisfied, and the role assigned to the subject
	 * has the required permissions for the requested action, scope, and resource
	 * characteristics.
	 *
	 * The test ensures that when a role's permission collection includes a specific
	 * permission for the requested operation (e.g., 'read') and all relevant rules
	 * validate successfully, the service correctly grants the access check.
	 */
	#[Test]
	public function testCheckReturnsTrueWhenAllRulesAreSatisfied(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId
		);

		$rule = $this->createStub(AuthorizationRuleInterface::class);

		$rule->method('isSatisfied')
			->willReturn(true)
		;

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([$rule])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertTrue($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service caches results
	 * for subsequent identical authorization checks.
	 *
	 * The test ensures that the `Authorization` service performs the necessary queries
	 * and computations only once, and subsequent checks for the same action, subject,
	 * and resource reuse the cached result, thereby improving performance.
	 */
	#[Test]
	public function testCheckCachesResults(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$assignment = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId
		);

		$roleRepository = $this->createMock(RoleRepositoryInterface::class);

		$roleRepository->expects($this->once())
			->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->expects($this->once())
			->method('getBySubjectId')
			->willReturn(
				RoleAssignmentCollection::from([$assignment])
			)
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertTrue($service->check('read', $subject, $resource));
		$this->assertTrue($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service returns `true`
	 * when a subject has a valid, non-expired role assignment with the required
	 * permission for the specified action, scope, and resource characteristics.
	 *
	 * The test ensures that if a role assignment is within its validity period
	 * and the assigned role contains a matching permission (e.g., 'read'), the service
	 * correctly grants the access check.
	 */
	#[Test]
	public function testCheckReturnsTrueWithNonExpiredAssignment(): void
	{
		$permissions = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role = Role::create(
			$this->roleId,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions,
			false
		);
		$futureDate          = DateTime::fromString('+1 day');
		$assignment          = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId,
			null,
			$futureDate
		);

		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturn($role)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertTrue($service->check('read', $subject, $resource));
	}

	/**
	 * Tests whether the `check` method of the `Authorization` service iterates through
	 * multiple role assignments for a subject until a matching permission is found
	 * for a requested action, scope, and resource.
	 *
	 * The test ensures that:
	 * - If a matching permission exists in any of the assigned roles, the service
	 *   authorizes the access (returns `true`) for the corresponding action.
	 * - If no matching permission exists across all assignments, the service denies
	 *   the access (returns `false`).
	 */
	#[Test]
	public function testCheckIteratesMultipleAssignmentsUntilMatchFound(): void
	{
		$roleId2       = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FEZ');
		$assignmentId2 = RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FGA');
		$permissions1  = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'write',
				'resource' => 'all',
			]),
		]);
		$permissions2 = PermissionCollection::from([
			Permission::fromNative([
				'scope'    => 'post',
				'action'   => 'read',
				'resource' => 'all',
			]),
		]);
		$role1 = Role::create(
			$this->roleId,
			RoleName::fromString('Viewer'),
			RoleSlug::fromString('webify.viewer'),
			$permissions1,
			false
		);
		$role2 = Role::create(
			$roleId2,
			RoleName::fromString('Editor'),
			RoleSlug::fromString('webify.editor'),
			$permissions2,
			false
		);
		$assignment1 = RoleAssignment::assign(
			$this->assignmentId,
			$this->roleId,
			$this->subjectId
		);
		$assignment2 = RoleAssignment::assign(
			$assignmentId2,
			$roleId2,
			$this->subjectId
		);
		$roleRepository = $this->createStub(RoleRepositoryInterface::class);

		$roleRepository->method('getById')
			->willReturnCallback(
				fn (RoleId $id): Role => match ($id->toNative()) {
					$this->roleId->toNative() => $role1,
					$roleId2->toNative()      => $role2,
					default                   => throw RoleNotFoundException::forId($id->toNative()),
				}
			)
		;

		$assignmentRepository = $this->createStub(RoleAssignmentRepositoryInterface::class);

		$assignmentRepository->method('getBySubjectId')
			->willReturn(RoleAssignmentCollection::from([$assignment1, $assignment2]))
		;

		$ruleRegistry = $this->createStub(AuthorizationRuleRegistryInterface::class);

		$ruleRegistry->method('getAllApplicableTo')
			->willReturn([])
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($this->subjectId->toNative())
		;
		$subject->method('tenantId')
			->willReturn(null)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceScope')
			->willReturn('post')
		;
		$resource->method('resourceType')
			->willReturn('all')
		;
		$resource->method('resourceId')
			->willReturn('some-resource-id')
		;
		$resource->method('ownerId')
			->willReturn(null)
		;
		$resource->method('tenantId')
			->willReturn(null)
		;

		$service = new Authorization($roleRepository, $assignmentRepository, $ruleRegistry);

		$this->assertTrue($service->check('read', $subject, $resource));
		$this->assertTrue($service->check('write', $subject, $resource));
		$this->assertFalse($service->check('delete', $subject, $resource));
	}
}
