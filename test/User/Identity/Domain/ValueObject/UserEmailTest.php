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
use Webify\User\Identity\Domain\Exception\InvalidUserEmailException;
use Webify\User\Identity\Domain\ValueObject\UserEmail;

/**
 * UserEmailTest tests the functionality of the UserEmail value object.
 *
 * @internal
 */
#[CoversClass(UserEmail::class)]
#[CoversMethod(UserEmail::class, 'fromString')]
final class UserEmailTest extends TestCase
{
	/**
	 * Tests the creation of a valid user ID from a string.
	 */
	#[Test]
	public function testCreateValidUserEmail(): void
	{
		$userEmail = UserEmail::fromString('test@example.com');

		$this->assertInstanceOf(UserEmail::class, $userEmail);
	}

	/**
	 * Test user ID with an invalid format.
	 */
	#[Test]
	public function testInvalidUserIdFormatThrowsException(): void
	{
		$this->expectException(InvalidUserEmailException::class);
		UserEmail::fromString('invalid-email');
	}
}
