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
use Webify\User\Identity\Domain\ValueObject\PasswordHash;

/**
 * PasswordHashTest tests the functionality of the HashedPassword value object.
 *
 * @internal
 */
#[CoversClass(PasswordHash::class)]
#[CoversMethod(PasswordHash::class, 'fromHash')]
#[CoversMethod(PasswordHash::class, 'equals')]
#[CoversMethod(PasswordHash::class, '__toString')]
#[CoversMethod(PasswordHash::class, 'toNative')]
final class PasswordHashTest extends TestCase
{
	#[Test]
	public function testCreateValidHashedPassword(): void
	{
		$hash           = password_hash('test_password', PASSWORD_DEFAULT);
		$hashedPassword = PasswordHash::fromHash($hash);

		$this->assertInstanceOf(PasswordHash::class, $hashedPassword);
		$this->assertSame($hash, $hashedPassword->toNative());
	}

	#[Test]
	public function testEmptyHashThrowsException(): void
	{
		$this->expectException(InvalidPasswordHashException::class);
		PasswordHash::fromHash('');
	}

	#[Test]
	public function testToNativeReturnsStringValue(): void
	{
		$hash           = password_hash('test_password', PASSWORD_DEFAULT);
		$hashedPassword = PasswordHash::fromHash($hash);

		$this->assertSame($hash, $hashedPassword->toNative());
	}

	#[Test]
	public function testStringRepresentation(): void
	{
		$hash           = password_hash('test_password', PASSWORD_DEFAULT);
		$hashedPassword = PasswordHash::fromHash($hash);

		$this->assertSame($hash, (string) $hashedPassword);
	}

	#[Test]
	public function testEqualsReturnsTrueForSameHash(): void
	{
		$hash            = password_hash('test_password', PASSWORD_DEFAULT);
		$hashedPassword1 = PasswordHash::fromHash($hash);
		$hashedPassword2 = PasswordHash::fromHash($hash);

		$this->assertTrue($hashedPassword1->equals($hashedPassword2));
	}

	#[Test]
	public function testEqualsReturnsFalseForDifferentHash(): void
	{
		$hashedPassword1 = PasswordHash::fromHash('hash1');
		$hashedPassword2 = PasswordHash::fromHash('hash2');

		$this->assertFalse($hashedPassword1->equals($hashedPassword2));
	}
}
