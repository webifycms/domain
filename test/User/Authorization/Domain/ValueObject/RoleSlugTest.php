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
use Webify\User\Authorization\Domain\Exception\InvalidRoleSlugException;
use Webify\User\Authorization\Domain\ValueObject\RoleSlug;

/**
 * RoleSlugTest tests the functionality of the RoleSlug value object.
 *
 * @internal
 */
#[CoversClass(RoleSlug::class)]
#[CoversMethod(RoleSlug::class, 'fromString')]
#[CoversMethod(RoleSlug::class, 'toNative')]
#[CoversMethod(RoleSlug::class, 'equals')]
#[CoversMethod(RoleSlug::class, '__toString')]
final class RoleSlugTest extends TestCase
{
	/**
	 * Tests creating a valid RoleSlug from string.
	 */
	#[Test]
	public function testCreateValidRoleSlugFromString(): void
	{
		$roleSlug = RoleSlug::fromString('admin');

		$this->assertInstanceOf(RoleSlug::class, $roleSlug);
		$this->assertSame('admin', $roleSlug->toNative());
	}

	/**
	 * Tests creating RoleSlug with multiple hyphens.
	 */
	#[Test]
	public function testCreateRoleSlugWithMultipleHyphens(): void
	{
		$roleSlug = RoleSlug::fromString('super-admin-user');

		$this->assertSame('super-admin-user', $roleSlug->toNative());
	}

	/**
	 * Tests that an exception is thrown when creating RoleSlug with uppercase letters.
	 */
	#[Test]
	public function testUppercaseRoleSlugThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('Admin');
	}

	/**
	 * Tests that an exception is thrown when creating RoleSlug starting with hyphen.
	 */
	#[Test]
	public function testRoleSlugStartingWithHyphenThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('-admin');
	}

	/**
	 * Tests that an exception is thrown when creating RoleSlug ending with hyphen.
	 */
	#[Test]
	public function testRoleSlugEndingWithHyphenThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('admin-');
	}

	/**
	 * Tests that an exception is thrown when creating RoleSlug with consecutive hyphens.
	 */
	#[Test]
	public function testRoleSlugWithConsecutiveHyphensThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('admin--user');
	}

	/**
	 * Tests that an exception is thrown when creating RoleSlug with empty string.
	 */
	#[Test]
	public function testEmptyRoleSlugThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('');
	}

	/**
	 * Tests equals method returns true for same values.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValues(): void
	{
		$roleSlug1 = RoleSlug::fromString('admin');
		$roleSlug2 = RoleSlug::fromString('admin');

		$this->assertTrue($roleSlug1->equals($roleSlug2));
	}

	/**
	 * Tests equals method returns false for different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$roleSlug1 = RoleSlug::fromString('admin');
		$roleSlug2 = RoleSlug::fromString('editor');

		$this->assertFalse($roleSlug1->equals($roleSlug2));
	}

	/**
	 * Tests string representation.
	 */
	#[Test]
	public function testToStringReturnsStringValue(): void
	{
		$roleSlug = RoleSlug::fromString('super-admin');

		$this->assertSame('super-admin', (string) $roleSlug);
	}
}
