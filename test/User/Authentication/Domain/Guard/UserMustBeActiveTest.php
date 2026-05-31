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

namespace Webify\Test\User\Authentication\Domain\Guard;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authentication\Domain\Exception\AuthenticationFailedException;
use Webify\User\Authentication\Domain\Guard\UserMustBeActive;
use Webify\User\Authentication\Domain\ValueObject\UserStatus;

/**
 * Tests for the UserMustBeActive guard.
 *
 * @internal
 */
#[CoversClass(UserMustBeActive::class)]
#[CoversMethod(UserMustBeActive::class, 'guard')]
final class UserMustBeActiveTest extends TestCase
{
	/**
	 * Tests that the guard passes when the user status is active.
	 */
	#[Test]
	public function testGuardPassesWhenStatusIsActive(): void
	{
		$guard = new UserMustBeActive();

		$this->expectNotToPerformAssertions();
		$guard->guard(UserStatus::Active);
	}

	/**
	 * Tests that the guard throws an exception when the user status is not active.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenStatusIsNotActive(): void
	{
		$guard = new UserMustBeActive();

		$this->expectException(AuthenticationFailedException::class);
		$guard->guard(UserStatus::Unverified);
	}
}
