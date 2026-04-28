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

namespace Webify\Test\User\Authorization\Domain\ValueObject;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authorization\Domain\Exception\InvalidRoleAssignmentIdException;
use Webify\User\Authorization\Domain\ValueObject\RoleAssignmentId;

/**
 * RoleAssignmentIdTest tests the functionality of the RoleAssignmentId value object.
 *
 * @internal
 */
#[CoversClass(RoleAssignmentId::class)]
#[CoversMethod(RoleAssignmentId::class, 'fromString')]
#[CoversMethod(RoleAssignmentId::class, 'toNative')]
#[CoversMethod(RoleAssignmentId::class, 'equals')]
#[CoversMethod(RoleAssignmentId::class, '__toString')]
final class RoleAssignmentIdTest extends TestCase
{
	/**
	 * Tests the creation of a valid RoleAssignmentId instance from a string.
	 */
	#[Test]
	public function testCreateValidRoleAssignmentIdFromString(): void
	{
		$id = RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertInstanceOf(RoleAssignmentId::class, $id);
		$this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', $id->toNative());
	}

	/**
	 * Tests that an attempt to create a RoleAssignmentId with an invalid string
	 * throws an InvalidRoleAssignmentIdException.
	 */
	#[Test]
	public function testInvalidRoleAssignmentIdThrowsException(): void
	{
		$this->expectException(InvalidRoleAssignmentIdException::class);
		RoleAssignmentId::fromString('invalid-id');
	}

	/**
	 * Tests that an attempt to create a RoleAssignmentId with a string that is too short
	 * throws an InvalidRoleAssignmentIdException.
	 */
	#[Test]
	public function testTooShortRoleAssignmentIdThrowsException(): void
	{
		$this->expectException(InvalidRoleAssignmentIdException::class);
		RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FA');
	}

	/**
	 * Tests that the equals method returns true when comparing two RoleAssignmentId instances
	 * created from the same string value.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValues(): void
	{
		$id1 = RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$id2 = RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertTrue($id1->equals($id2));
	}

	/**
	 * Tests that the equals method returns false when comparing two RoleAssignmentId objects with different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$id1 = RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$id2 = RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FBW');

		$this->assertFalse($id1->equals($id2));
	}

	/**
	 * Tests that the toString method of RoleAssignmentId returns the expected string value.
	 */
	#[Test]
	public function testToStringReturnsStringValue(): void
	{
		$id = RoleAssignmentId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', (string) $id);
	}
}
