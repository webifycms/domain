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

namespace Webify\Test\User\Identity\Domain\Guard;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Identity\Domain\Exception\WeakPasswordException;
use Webify\User\Identity\Domain\Guard\PasswordMustBeStrong;
use Webify\User\Identity\Domain\Policy\PasswordPolicy;

/**
 * PasswordMustBeStrongTest tests the PasswordMustBeStrong guard.
 *
 * @internal
 */
#[CoversClass(PasswordMustBeStrong::class)]
#[CoversMethod(PasswordMustBeStrong::class, 'guard')]
final class PasswordMustBeStrongTest extends TestCase
{
	/**
	 * Tests that the guard passes when the password meets all policy requirements.
	 */
	#[Test]
	public function testGuardPassesWhenPasswordSatisfiesAllRequirements(): void
	{
		$guard = new PasswordMustBeStrong(new PasswordPolicy());

		$this->expectNotToPerformAssertions();
		$guard->guard('StrongPass1!');
	}

	/**
	 * Tests that the guard throws an exception when the password is too short.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenPasswordIsTooShort(): void
	{
		$guard = new PasswordMustBeStrong(new PasswordPolicy());

		$this->expectException(WeakPasswordException::class);
		$guard->guard('Ab1!');
	}

	/**
	 * Tests that the guard throws an exception when the password exceeds the maximum length.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenPasswordIsTooLong(): void
	{
		$guard = new PasswordMustBeStrong(new PasswordPolicy());

		$this->expectException(WeakPasswordException::class);
		$guard->guard('A1!' . str_repeat('x', 130));
	}

	/**
	 * Tests that the guard throws an exception when the password has insufficient uppercase characters.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenMissingUppercase(): void
	{
		$guard = new PasswordMustBeStrong(new PasswordPolicy());

		$this->expectException(WeakPasswordException::class);
		$guard->guard('lowercase123!');
	}

	/**
	 * Tests that the guard throws an exception when the password has insufficient lowercase characters.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenMissingLowercase(): void
	{
		$guard = new PasswordMustBeStrong(new PasswordPolicy());

		$this->expectException(WeakPasswordException::class);
		$guard->guard('UPPERCASE12!');
	}

	/**
	 * Tests that the guard throws an exception when the password has no digits.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenMissingDigit(): void
	{
		$guard = new PasswordMustBeStrong(new PasswordPolicy());

		$this->expectException(WeakPasswordException::class);
		$guard->guard('UPPERlower!!');
	}

	/**
	 * Tests that the guard throws an exception when the password has no special characters.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenMissingSpecialCharacter(): void
	{
		$guard = new PasswordMustBeStrong(new PasswordPolicy());

		$this->expectException(WeakPasswordException::class);
		$guard->guard('UPPERlower12');
	}
}
