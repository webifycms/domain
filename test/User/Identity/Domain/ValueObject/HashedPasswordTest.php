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
use Webify\User\Identity\Domain\Exception\InvalidPasswordHashException;
use Webify\User\Identity\Domain\ValueObject\HashedPassword;

/**
 * HashedPasswordTest tests the functionality of the HashedPassword value object.
 *
 * @internal
 */
#[CoversClass(HashedPassword::class)]
#[CoversMethod(HashedPassword::class, 'fromHash')]
#[CoversMethod(HashedPassword::class, 'equals')]
#[CoversMethod(HashedPassword::class, '__toString')]
#[CoversMethod(HashedPassword::class, 'toNative')]
final class HashedPasswordTest extends TestCase
{
	#[Test]
	public function testCreateValidHashedPassword(): void
	{
		$hash           = password_hash('test_password', PASSWORD_DEFAULT);
		$hashedPassword = HashedPassword::fromHash($hash);

		$this->assertInstanceOf(HashedPassword::class, $hashedPassword);
		$this->assertSame($hash, $hashedPassword->toNative());
	}

	#[Test]
	public function testEmptyHashThrowsException(): void
	{
		$this->expectException(InvalidPasswordHashException::class);
		HashedPassword::fromHash('');
	}

	#[Test]
	public function testToNativeReturnsStringValue(): void
	{
		$hash           = password_hash('test_password', PASSWORD_DEFAULT);
		$hashedPassword = HashedPassword::fromHash($hash);

		$this->assertSame($hash, $hashedPassword->toNative());
	}

	#[Test]
	public function testStringRepresentation(): void
	{
		$hash           = password_hash('test_password', PASSWORD_DEFAULT);
		$hashedPassword = HashedPassword::fromHash($hash);

		$this->assertSame($hash, (string) $hashedPassword);
	}

	#[Test]
	public function testEqualsReturnsTrueForSameHash(): void
	{
		$hash            = password_hash('test_password', PASSWORD_DEFAULT);
		$hashedPassword1 = HashedPassword::fromHash($hash);
		$hashedPassword2 = HashedPassword::fromHash($hash);

		$this->assertTrue($hashedPassword1->equals($hashedPassword2));
	}

	#[Test]
	public function testEqualsReturnsFalseForDifferentHash(): void
	{
		$hashedPassword1 = HashedPassword::fromHash('hash1');
		$hashedPassword2 = HashedPassword::fromHash('hash2');

		$this->assertFalse($hashedPassword1->equals($hashedPassword2));
	}
}
