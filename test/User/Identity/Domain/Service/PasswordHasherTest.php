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

namespace Webify\Test\User\Identity\Domain\Service;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Identity\Domain\Service\PasswordHasher;
use Webify\User\Identity\Domain\ValueObject\PasswordHash;

/**
 * PasswordHashTest tests the functionality of the PasswordHash service.
 *
 * @internal
 *
 * @todo Should find out how to test the password_hash failure to test expected exception.
 */
#[CoversClass(PasswordHasher::class)]
#[CoversMethod(PasswordHasher::class, 'hash')]
#[CoversMethod(PasswordHasher::class, 'verify')]
final class PasswordHasherTest extends TestCase
{
	/**
	 * Password hash service instance.
	 */
	private PasswordHasher $passwordHash;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->passwordHash = new PasswordHasher();
	}

	#[Test]
	public function testHashReturnsHashedPassword(): void
	{
		$password       = 'test_password_123';
		$hashedPassword = $this->passwordHash->hash($password);

		$this->assertInstanceOf(PasswordHash::class, $hashedPassword);
		$this->assertNotEmpty($hashedPassword->toNative());
	}

	#[Test]
	public function testVerifyReturnsTrueForCorrectPassword(): void
	{
		$password       = 'test_password_123';
		$hashedPassword = $this->passwordHash->hash($password);

		$this->assertTrue($this->passwordHash->verify($password, $hashedPassword->toNative()));
	}

	#[Test]
	public function testVerifyReturnsFalseForIncorrectPassword(): void
	{
		$correctPassword = 'correctPassword';
		$wrongPassword   = 'wrongPassword';
		$hashedPassword  = $this->passwordHash->hash($correctPassword);

		$this->assertFalse($this->passwordHash->verify($wrongPassword, $hashedPassword->toNative()));
	}

	#[Test]
	public function testHashGeneratesUniqueHashes(): void
	{
		$password = 'test_password_123';
		$hash1    = $this->passwordHash->hash($password);
		$hash2    = $this->passwordHash->hash($password);

		$this->assertNotSame($hash1->toNative(), $hash2->toNative());
	}

	#[Test]
	public function testHashedPasswordCanBeVerified(): void
	{
		$password       = 'test_password_123';
		$hashedPassword = $this->passwordHash->hash($password);

		$result = $this->passwordHash->verify($password, $hashedPassword->toNative());

		$this->assertTrue($result);
	}
}
