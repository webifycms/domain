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
use Webify\User\Authorization\Domain\Exception\InvalidRoleIdException;
use Webify\User\Authorization\Domain\ValueObject\RoleId;

/**
 * RoleIdTest tests the functionality of the RoleId value object.
 *
 * @internal
 */
#[CoversClass(RoleId::class)]
#[CoversMethod(RoleId::class, 'fromString')]
#[CoversMethod(RoleId::class, 'toNative')]
#[CoversMethod(RoleId::class, 'equals')]
#[CoversMethod(RoleId::class, '__toString')]
final class RoleIdTest extends TestCase
{
	/**
	 * Tests creating a valid RoleId from string.
	 */
	#[Test]
	public function testCreateValidRoleIdFromString(): void
	{
		$roleId = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertInstanceOf(RoleId::class, $roleId);
		$this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', $roleId->toNative());
	}

	/**
	 * Tests that an exception is thrown when creating RoleId with invalid format.
	 */
	#[Test]
	public function testInvalidRoleIdThrowsException(): void
	{
		$this->expectException(InvalidRoleIdException::class);
		RoleId::fromString('invalid-id');
	}

	/**
	 * Tests that too short RoleId throws exception.
	 */
	#[Test]
	public function testTooShortRoleIdThrowsException(): void
	{
		$this->expectException(InvalidRoleIdException::class);
		RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FA');
	}

	/**
	 * Tests equals method returns true for same values.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValues(): void
	{
		$roleId1 = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$roleId2 = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertTrue($roleId1->equals($roleId2));
	}

	/**
	 * Tests equals method returns false for different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$roleId1 = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$roleId2 = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FBW');

		$this->assertFalse($roleId1->equals($roleId2));
	}

	/**
	 * Tests string representation.
	 */
	#[Test]
	public function testToStringReturnsStringValue(): void
	{
		$roleId = RoleId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', (string) $roleId);
	}
}
