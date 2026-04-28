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
use Webify\User\Authorization\Domain\Exception\InvalidTenantIdException;
use Webify\User\Authorization\Domain\ValueObject\TenantId;

/**
 * TenantIdTest tests the functionality of the TenantId value object.
 *
 * @internal
 */
#[CoversClass(TenantId::class)]
#[CoversMethod(TenantId::class, 'fromString')]
#[CoversMethod(TenantId::class, 'toNative')]
#[CoversMethod(TenantId::class, 'equals')]
#[CoversMethod(TenantId::class, '__toString')]
final class TenantIdTest extends TestCase
{
	/**
	 * Tests the creation of a valid TenantId object from a string.
	 */
	#[Test]
	public function testCreateValidTenantIdFromString(): void
	{
		$id = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertInstanceOf(TenantId::class, $id);
		$this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', $id->toNative());
	}

	/**
	 * Tests that an InvalidTenantIdException is thrown when trying to create a TenantId
	 * instance with an invalid ID string.
	 */
	#[Test]
	public function testInvalidTenantIdThrowsException(): void
	{
		$this->expectException(InvalidTenantIdException::class);
		TenantId::fromString('invalid-id');
	}

	/**
	 * Tests that an exception is thrown when attempting to create a TenantId
	 * with a string that is too short to be valid.
	 */
	#[Test]
	public function testTooShortTenantIdThrowsException(): void
	{
		$this->expectException(InvalidTenantIdException::class);
		TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FA');
	}

	/**
	 * Tests that the equals method returns true when comparing two instances
	 * of TenantId created with the same value.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValues(): void
	{
		$id1 = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$id2 = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertTrue($id1->equals($id2));
	}

	/**
	 * Tests that the equals method returns false when comparing two instances
	 * of TenantId created with different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$id1 = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$id2 = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FBW');

		$this->assertFalse($id1->equals($id2));
	}

	/**
	 * Tests that the toString method returns the string representation
	 * of the TenantId value.
	 */
	#[Test]
	public function testToStringReturnsStringValue(): void
	{
		$id = TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', (string) $id);
	}
}
