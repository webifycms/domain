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
use Webify\User\Authorization\Domain\Exception\InvalidRoleNameException;
use Webify\User\Authorization\Domain\ValueObject\RoleName;

/**
 * RoleNameTest tests the functionality of the RoleName value object.
 *
 * @internal
 */
#[CoversClass(RoleName::class)]
#[CoversMethod(RoleName::class, 'fromString')]
#[CoversMethod(RoleName::class, 'toNative')]
#[CoversMethod(RoleName::class, 'equals')]
#[CoversMethod(RoleName::class, '__toString')]
final class RoleNameTest extends TestCase
{
	/**
	 * Tests creating a valid RoleName from string.
	 */
	#[Test]
	public function testCreateValidRoleNameFromString(): void
	{
		$roleName = RoleName::fromString('Admin');

		$this->assertInstanceOf(RoleName::class, $roleName);
		$this->assertSame('Admin', $roleName->toNative());
	}

	/**
	 * Tests that an exception is thrown when creating RoleName with empty string.
	 */
	#[Test]
	public function testEmptyRoleNameThrowsException(): void
	{
		$this->expectException(InvalidRoleNameException::class);
		RoleName::fromString('');
	}

	/**
	 * Tests equals method returns true for same values.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValues(): void
	{
		$roleName1 = RoleName::fromString('Admin');
		$roleName2 = RoleName::fromString('Admin');

		$this->assertTrue($roleName1->equals($roleName2));
	}

	/**
	 * Tests equals method returns false for different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$roleName1 = RoleName::fromString('Admin');
		$roleName2 = RoleName::fromString('Editor');

		$this->assertFalse($roleName1->equals($roleName2));
	}

	/**
	 * Tests string representation.
	 */
	#[Test]
	public function testToStringReturnsStringValue(): void
	{
		$roleName = RoleName::fromString('Super Admin');

		$this->assertSame('Super Admin', (string) $roleName);
	}
}
