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

namespace Webify\Test\User\Identity\Domain\ValueObject;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use ValueError;
use Webify\User\Identity\Domain\ValueObject\UserStatus;

/**
 * UserStatusTest tests the functionality of the UserStatus value object.
 *
 * @internal
 */
#[CoversClass(UserStatus::class)]
#[CoversMethod(UserStatus::class, 'isActive')]
#[CoversMethod(UserStatus::class, 'isInactive')]
#[CoversMethod(UserStatus::class, 'isDeactivated')]
final class UserStatusTest extends TestCase
{
	/**
	 * Tests whether the `UserStatus::Active` status is correctly identified as active.
	 */
	#[Test]
	public function testActiveStatusIsActive(): void
	{
		$status = UserStatus::Active;

		$this->assertTrue($status->isActive());
		$this->assertFalse($status->isInactive());
		$this->assertFalse($status->isDeactivated());
	}

	/**
	 * Tests whether the `UserStatus::Inactive` status is correctly identified as inactive.
	 */
	#[Test]
	public function testInactiveStatusIsInactive(): void
	{
		$status = UserStatus::Inactive;

		$this->assertFalse($status->isActive());
		$this->assertTrue($status->isInactive());
		$this->assertFalse($status->isDeactivated());
	}

	/**
	 * Tests whether the `UserStatus::Deactivated` status is correctly identified as deactivated.
	 */
	#[Test]
	public function testDeactivatedStatusIsDeactivated(): void
	{
		$status = UserStatus::Deactivated;

		$this->assertFalse($status->isActive());
		$this->assertFalse($status->isInactive());
		$this->assertTrue($status->isDeactivated());
	}

	/**
	 * Tests status values.
	 */
	#[Test]
	public function testStatusValues(): void
	{
		$this->assertSame('active', UserStatus::Active->value);
		$this->assertSame('inactive', UserStatus::Inactive->value);
		$this->assertSame('deactivated', UserStatus::Deactivated->value);
	}

	/**
	 * Tests weather can create status from value.
	 */
	#[Test]
	public function testStatusFromValue(): void
	{
		$this->assertSame(UserStatus::Active, UserStatus::from('active'));
		$this->assertSame(UserStatus::Inactive, UserStatus::from('inactive'));
		$this->assertSame(UserStatus::Deactivated, UserStatus::from('deactivated'));
	}

	/**
	 * Tests throwing exception for invalid value.
	 */
	#[Test]
	public function testInvalidValueThrowsException(): void
	{
		$this->expectException(ValueError::class);
		UserStatus::from('invalid');
	}
}
