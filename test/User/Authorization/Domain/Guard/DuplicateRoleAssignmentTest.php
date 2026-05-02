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

namespace Webify\Test\User\Authorization\Domain\Guard;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authorization\Domain\Exception\DuplicateRoleAssignmentException;
use Webify\User\Authorization\Domain\Guard\DuplicateRoleAssignment;
use Webify\User\Authorization\Domain\Repository\RoleAssignmentRepositoryInterface;
use Webify\User\Authorization\Domain\ValueObject\{RoleId, SubjectId};

/**
 * DuplicateRoleAssignmentTest tests the functionality of the DuplicateRoleAssignment guard.
 *
 * @internal
 */
#[CoversClass(DuplicateRoleAssignment::class)]
#[CoversMethod(DuplicateRoleAssignment::class, 'guard')]
final class DuplicateRoleAssignmentTest extends TestCase
{
	/**
	 * Tests that the guard passes when no duplicate role assignment exists.
	 */
	#[Test]
	public function testGuardPassesWhenNoDuplicateExists(): void
	{
		$roleId     = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$subjectId  = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FBW');
		$repository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$repository->expects($this->once())
			->method('isExists')
			->with($roleId, $subjectId)
			->willReturn(false)
		;

		$guard = new DuplicateRoleAssignment($repository);

		$guard->guard($roleId, $subjectId);
	}

	/**
	 * Tests that the guard throws an exception when a duplicate role assignment exists.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenDuplicateExists(): void
	{
		$roleId     = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$subjectId  = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FBW');
		$repository = $this->createMock(RoleAssignmentRepositoryInterface::class);

		$repository->expects($this->once())
			->method('isExists')
			->with($roleId, $subjectId)
			->willReturn(true)
		;

		$guard = new DuplicateRoleAssignment($repository);

		$this->expectException(DuplicateRoleAssignmentException::class);
		$guard->guard($roleId, $subjectId);
	}
}
