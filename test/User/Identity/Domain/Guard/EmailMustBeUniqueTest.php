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
use Webify\User\Identity\Domain\Exception\EmailMustBeUniqueException;
use Webify\User\Identity\Domain\Guard\EmailMustBeUnique;
use Webify\User\Identity\Domain\Repository\UserRepositoryInterface;
use Webify\User\Identity\Domain\ValueObject\UserEmail;

/**
 * EmailMustBeUniqueTest tests the functionality of the EmailMustBeUnique guard.
 *
 * @internal
 */
#[CoversClass(EmailMustBeUnique::class)]
#[CoversMethod(EmailMustBeUnique::class, 'guard')]
final class EmailMustBeUniqueTest extends TestCase
{
	/**
	 * Tests that the guard passes when the email does not exist in the repository.
	 */
	#[Test]
	public function testGuardPassesWhenEmailIsUnique(): void
	{
		$email      = UserEmail::fromString('test@example.com');
		$repository = $this->createMock(UserRepositoryInterface::class);

		$repository->expects($this->once())
			->method('isExists')
			->with($email)
			->willReturn(false)
		;

		$guard = new EmailMustBeUnique($repository);

		$guard->guard($email);
	}

	/**
	 * Tests that the guard throws an exception when the email already exists in the repository.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenEmailIsNotUnique(): void
	{
		$email      = UserEmail::fromString('test@example.com');
		$repository = $this->createMock(UserRepositoryInterface::class);

		$repository->expects($this->once())
			->method('isExists')
			->with($email)
			->willReturn(true)
		;

		$guard = new EmailMustBeUnique($repository);

		$this->expectException(EmailMustBeUniqueException::class);
		$guard->guard($email);
	}
}
