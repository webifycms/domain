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
#[CoversMethod(RoleSlug::class, 'getVendor')]
#[CoversMethod(RoleSlug::class, 'getSlug')]
final class RoleSlugTest extends TestCase
{
	/**
	 * Tests the creation of a valid RoleSlug instance from a string.
	 *
	 * @return void
	 */
	#[Test]
	public function testCreateValidRoleSlugFromString(): void
	{
		$roleSlug = RoleSlug::fromString('webify.admin');

		$this->assertInstanceOf(RoleSlug::class, $roleSlug);
		$this->assertSame(['vendor' => 'webify', 'slug' => 'admin'], $roleSlug->toNative());
	}

	/**
	 * Tests that the getVendor method correctly returns the vendor part of a role slug.
	 *
	 * @return void
	 */
	#[Test]
	public function testGetVendorReturnsVendorPart(): void
	{
		$roleSlug = RoleSlug::fromString('webify.admin');

		$this->assertSame('webify', $roleSlug->getVendor());
	}

	/**
	 * Tests that the getSlug method correctly returns the slug part of a role slug.
	 *
	 * @return void
	 */
	#[Test]
	public function testGetSlugReturnsSlugPart(): void
	{
		$roleSlug = RoleSlug::fromString('webify.admin');

		$this->assertSame('admin', $roleSlug->getSlug());
	}

	/**
	 * Tests that a role slug with multiple hyphens is correctly created and its native representation is returned.
	 *
	 * @return void
	 */
	#[Test]
	public function testCreateRoleSlugWithMultipleHyphens(): void
	{
		$roleSlug = RoleSlug::fromString('webify.super-admin-user');

		$this->assertSame(['vendor' => 'webify', 'slug' => 'super-admin-user'], $roleSlug->toNative());
	}

	/**
	 * Tests that an exception is thrown when creating a role slug without a vendor separator.
	 *
	 * @return void
	 */
	#[Test]
	public function testMissingSeparatorThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('admin');
	}

	/**
	 * Tests that an exception is thrown when the vendor part of a role slug is in uppercase.
	 *
	 * @return void
	 */
	#[Test]
	public function testUppercaseVendorThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('WEBIFY.admin');
	}

	/**
	 * Tests that an exception is thrown when attempting to create a role slug with uppercase characters.
	 *
	 * @return void
	 */
	#[Test]
	public function testUppercaseSlugThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('webify.Admin');
	}

	/**
	 * Tests that creating a RoleSlug with a vendor starting with a hyphen throws an InvalidRoleSlugException.
	 *
	 * @return void
	 */
	#[Test]
	public function testVendorStartingWithHyphenThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('-webify.admin');
	}

	/**
	 * Tests that creating a role slug starting with a hyphen throws an InvalidRoleSlugException.
	 *
	 * @return void
	 */
	#[Test]
	public function testSlugStartingWithHyphenThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('webify.-admin');
	}

	/**
	 * Tests that an exception is thrown when the vendor part of a role slug ends with a hyphen.
	 *
	 * @return void
	 */
	#[Test]
	public function testVendorEndingWithHyphenThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('webify-.admin');
	}

	/**
	 * Tests that an exception is thrown when a role slug ends with a hyphen.
	 *
	 * @return void
	 */
	#[Test]
	public function testSlugEndingWithHyphenThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('webify.admin-');
	}

	/**
	 * Tests that attempting to create a role slug with consecutive hyphens in the vendor part throws an exception.
	 *
	 * @return void
	 */
	#[Test]
	public function testVendorWithConsecutiveHyphensThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('ac--me.admin');
	}

	/**
	 * Tests that an exception is thrown when a role slug contains consecutive hyphens.
	 *
	 * @return void
	 */
	#[Test]
	public function testSlugWithConsecutiveHyphensThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('webify.admin--user');
	}

	/**
	 * Tests that an empty string passed to the fromString method throws an InvalidRoleSlugException.
	 *
	 * @return void
	 */
	#[Test]
	public function testEmptyStringThrowsException(): void
	{
		$this->expectException(InvalidRoleSlugException::class);
		RoleSlug::fromString('');
	}

	/**
	 * Tests that the equals method returns true when comparing two RoleSlug instances with the same value.
	 *
	 * @return void
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValues(): void
	{
		$roleSlug1 = RoleSlug::fromString('webify.admin');
		$roleSlug2 = RoleSlug::fromString('webify.admin');

		$this->assertTrue($roleSlug1->equals($roleSlug2));
	}

	/**
	 * Tests that the equals method returns false when comparing role slugs with different vendors.
	 *
	 * @return void
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentVendors(): void
	{
		$roleSlug1 = RoleSlug::fromString('webify.admin');
		$roleSlug2 = RoleSlug::fromString('corp.admin');

		$this->assertFalse($roleSlug1->equals($roleSlug2));
	}

	/**
	 * Tests that the equals method returns false when comparing two role slugs with different values.
	 *
	 * @return void
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentSlugs(): void
	{
		$roleSlug1 = RoleSlug::fromString('webify.admin');
		$roleSlug2 = RoleSlug::fromString('webify.editor');

		$this->assertFalse($roleSlug1->equals($roleSlug2));
	}

	/**
	 * Tests that the __toString method correctly returns the string representation of a role slug.
	 *
	 * @return void
	 */
	#[Test]
	public function testToStringReturnsStringValue(): void
	{
		$roleSlug = RoleSlug::fromString('webify.super-admin');

		$this->assertSame('webify.super-admin', (string) $roleSlug);
	}
}
