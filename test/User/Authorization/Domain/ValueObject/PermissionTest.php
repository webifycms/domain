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
use Webify\User\Authorization\Domain\Exception\InvalidPermissionException;
use Webify\User\Authorization\Domain\ValueObject\Permission;

/**
 * PermissionTest tests the functionality of the Permission value object.
 *
 * @internal
 */
#[CoversClass(Permission::class)]
#[CoversMethod(Permission::class, 'fromNative')]
#[CoversMethod(Permission::class, 'equals')]
#[CoversMethod(Permission::class, 'toNative')]
#[CoversMethod(Permission::class, 'wildcard')]
#[CoversMethod(Permission::class, 'scopedWildcard')]
final class PermissionTest extends TestCase
{
	/**
	 * Tests the creation of a valid Permission instance from an array and verifies its properties.
	 */
	#[Test]
	public function testCreateValidPermissionFromArray(): void
	{
		$permission = Permission::fromNative([
			'scope'    => 'post',
			'action'   => 'create',
			'resource' => 'article',
		]);

		$this->assertInstanceOf(Permission::class, $permission);
		$this->assertSame([
			'scope'    => 'post',
			'action'   => 'create',
			'resource' => 'article',
		], $permission->toNative());
	}

	/**
	 * Tests that an exception is thrown when attempting to create a Permission with an empty scope.
	 */
	#[Test]
	public function testEmptyScopeThrowsException(): void
	{
		$this->expectException(InvalidPermissionException::class);
		Permission::fromNative([
			'scope'    => '',
			'action'   => 'create',
			'resource' => 'article',
		]);
	}

	/**
	 * Tests that an exception is thrown when attempting to create a Permission with an empty action.
	 */
	#[Test]
	public function testEmptyActionThrowsException(): void
	{
		$this->expectException(InvalidPermissionException::class);
		Permission::fromNative([
			'scope'    => 'post',
			'action'   => '',
			'resource' => 'article',
		]);
	}

	/**
	 * Tests that an exception is thrown when attempting to create a Permission with an empty resource.
	 */
	#[Test]
	public function testEmptyResourceThrowsException(): void
	{
		$this->expectException(InvalidPermissionException::class);
		Permission::fromNative([
			'scope'    => 'post',
			'action'   => 'create',
			'resource' => '',
		]);
	}

	/**
	 * Tests that the toNative method of Permission returns the expected array value.
	 */
	#[Test]
	public function testToNativeReturnsArrayValue(): void
	{
		$permission = Permission::fromNative([
			'scope'    => 'user',
			'action'   => 'read',
			'resource' => 'profile',
		]);

		$this->assertSame([
			'scope'    => 'user',
			'action'   => 'read',
			'resource' => 'profile',
		], $permission->toNative());
	}

	/**
	 * Tests that the equals method returns true when comparing two Permission instances with the same values.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValues(): void
	{
		$permission1 = Permission::fromNative([
			'scope'    => 'post',
			'action'   => 'edit',
			'resource' => 'article',
		]);
		$permission2 = Permission::fromNative([
			'scope'    => 'post',
			'action'   => 'edit',
			'resource' => 'article',
		]);

		$this->assertTrue($permission1->equals($permission2));
	}

	/**
	 * Tests that the equals method returns false when comparing Permission instances with different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$permission1 = Permission::fromNative([
			'scope'    => 'post',
			'action'   => 'create',
			'resource' => 'article',
		]);
		$permission2 = Permission::fromNative([
			'scope'    => 'post',
			'action'   => 'delete',
			'resource' => 'article',
		]);

		$this->assertFalse($permission1->equals($permission2));
	}

	/**
	 * Tests that the wildcard method creates a Permission with wildcard values.
	 */
	#[Test]
	public function testWildcardCreatesPermissionWithWildcardValues(): void
	{
		$permission = Permission::wildcard();

		$this->assertSame([
			'scope'    => '*',
			'action'   => '*',
			'resource' => '*',
		], $permission->toNative());
	}

	/**
	 * Tests that the scopedWildcard method creates a Permission with wildcard action and resource for a specific scope.
	 */
	#[Test]
	public function testScopedWildcardCreatesPermissionWithScopeAndWildcards(): void
	{
		$permission = Permission::scopedWildcard('user');

		$this->assertSame([
			'scope'    => 'user',
			'action'   => '*',
			'resource' => '*',
		], $permission->toNative());
	}
}
