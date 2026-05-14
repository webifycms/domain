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
use Webify\User\Identity\Infrastructure\Service\PasswordHasher;

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

	/**
	 * Tests that the hash method returns a non-empty hashed password
	 * for the provided input string.
	 */
	#[Test]
	public function testHashReturnsHashedPassword(): void
	{
		$password       = 'test_password_123';
		$hashedPassword = $this->passwordHash->hash($password);

		$this->assertNotEmpty($hashedPassword);
	}

	/**
	 * Tests that the verify method returns true when provided with the correct password and its hashed counterpart.
	 */
	#[Test]
	public function testVerifyReturnsTrueForCorrectPassword(): void
	{
		$password       = 'test_password_123';
		$hashedPassword = $this->passwordHash->hash($password);

		$this->assertTrue($this->passwordHash->verify($password, $hashedPassword));
	}

	/**
	 * Tests that the password verification method returns false
	 * when a wrong password is provided for a given hashed password.
	 */
	#[Test]
	public function testVerifyReturnsFalseForIncorrectPassword(): void
	{
		$correctPassword = 'correctPassword';
		$wrongPassword   = 'wrongPassword';
		$hashedPassword  = $this->passwordHash->hash($correctPassword);

		$this->assertFalse($this->passwordHash->verify($wrongPassword, $hashedPassword));
	}

	/**
	 * Tests that the `hash` method generates unique hashes for the same input password.
	 */
	#[Test]
	public function testHashGeneratesUniqueHashes(): void
	{
		$password = 'test_password_123';
		$hash1    = $this->passwordHash->hash($password);
		$hash2    = $this->passwordHash->hash($password);

		$this->assertNotSame($hash1, $hash2);
	}

	/**
	 * Tests that a hashed password can be successfully verified against its plain text version.
	 */
	#[Test]
	public function testHashedPasswordCanBeVerified(): void
	{
		$password       = 'test_password_123';
		$hashedPassword = $this->passwordHash->hash($password);
		$result         = $this->passwordHash->verify($password, $hashedPassword);

		$this->assertTrue($result);
	}
}
