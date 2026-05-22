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

namespace Webify\Test\User\Authorization\Domain\Guard;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authorization\Domain\Exception\RoleAlreadyExistsException;
use Webify\User\Authorization\Domain\Guard\RoleAlreadyExists;
use Webify\User\Authorization\Domain\Repository\RoleRepositoryInterface;
use Webify\User\Authorization\Domain\ValueObject\RoleSlug;

/**
 * RoleAlreadyExistsTest tests the functionality of the RoleAlreadyExists guard.
 *
 * @internal
 */
#[CoversClass(RoleAlreadyExists::class)]
#[CoversMethod(RoleAlreadyExists::class, 'guard')]
final class RoleAlreadyExistsTest extends TestCase
{
	/**
	 * Tests that the guard passes when a role with the given slug does not exist.
	 */
	#[Test]
	public function testGuardPassesWhenRoleDoesNotExist(): void
	{
		$slug       = RoleSlug::fromString('webify.admin');
		$repository = $this->createMock(RoleRepositoryInterface::class);

		$repository->expects($this->once())
			->method('isExist')
			->with($slug)
			->willReturn(false)
		;

		$guard = new RoleAlreadyExists($repository);

		$guard->guard($slug);
	}

	/**
	 * Tests that the guard throws an exception when a role with the given slug already exists.
	 */
	#[Test]
	public function testGuardThrowsExceptionWhenRoleAlreadyExists(): void
	{
		$slug       = RoleSlug::fromString('webify.admin');
		$repository = $this->createMock(RoleRepositoryInterface::class);

		$repository->expects($this->once())
			->method('isExist')
			->with($slug)
			->willReturn(true)
		;

		$guard = new RoleAlreadyExists($repository);

		$this->expectException(RoleAlreadyExistsException::class);
		$guard->guard($slug);
	}
}
