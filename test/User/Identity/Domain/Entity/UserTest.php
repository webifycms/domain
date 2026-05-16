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

namespace Webify\Test\User\Identity\Domain\Entity;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Identity\Domain\Entity\User;
use Webify\User\Identity\Domain\Event\{UserDisplayNameWasChanged,
	UserEmailWasChanged,
	UserPasswordWasChanged,
	UserWasActivated,
	UserWasDeactivated,
	UserWasRegistered};
use Webify\User\Identity\Domain\Exception\{UserAlreadyActivatedException,
	UserAlreadyDeactivatedException,
	UserCannotBeDeactivatedException};
use Webify\User\Identity\Domain\ValueObject\{DisplayName, PasswordHash, UserEmail, UserId};
use Webify\User\Identity\Infrastructure\Service\PasswordHasher;

/**
 * UserTest tests the functionality of the User entity.
 *
 * @internal
 */
#[CoversClass(User::class)]
#[CoversMethod(User::class, 'activate')]
#[CoversMethod(User::class, 'deactivate')]
#[CoversMethod(User::class, 'changeDisplayName')]
#[CoversMethod(User::class, 'changeEmail')]
#[CoversMethod(User::class, 'changePassword')]
#[CoversMethod(User::class, 'getDisplayName')]
final class UserTest extends TestCase
{
	/**
	 * User instance.
	 */
	private User $user;

	/**
	 * Password hasher instance.
	 */
	private PasswordHasher $passwordHasher;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->passwordHasher = new PasswordHasher();
		$this->user           = User::register(
			UserId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			UserEmail::fromString('test@example.com'),
			PasswordHash::fromHash($this->passwordHasher->hash('password')),
			DisplayName::fromString('Test User')
		);
	}

	/**
	 * Tests user-registered event.
	 */
	#[Test]
	public function testUserRegisteredEventUser(): void
	{
		$events = $this->user->getDomainEvents();

		$this->assertCount(1, $events);
		$this->assertInstanceOf(UserWasRegistered::class, $events[0]);
	}

	/**
	 * Tests user initial status.
	 */
	#[Test]
	public function testUserInitialStatusIsInactive(): void
	{
		$this->assertTrue($this->user->getStatus()->isInactive());
	}

	/**
	 * Tests activating a user.
	 */
	#[Test]
	public function testActivateUser(): void
	{
		$this->user->activate();

		$this->assertTrue($this->user->getStatus()->isActive());

		$events = $this->user->getDomainEvents();

		$this->assertCount(2, $events);
		$this->assertInstanceOf(UserWasActivated::class, $events[1]);
	}

	/**
	 * Tests activating an already activated user throws an exception.
	 */
	#[Test]
	public function testActivateAlreadyActivatedUserThrowsException(): void
	{
		$this->user->activate();
		$this->expectException(UserAlreadyActivatedException::class);
		$this->user->activate();
	}

	/**
	 * Tests deactivating a user.
	 */
	#[Test]
	public function testDeactivateUser(): void
	{
		$this->user->activate();
		$this->user->deactivate();

		$this->assertTrue($this->user->getStatus()->isDeactivated());

		$events = $this->user->getDomainEvents();

		$this->assertCount(3, $events);
		$this->assertInstanceOf(UserWasDeactivated::class, $events[2]);
	}

	/**
	 * Tests deactivating an already deactivated user throws an exception.
	 */
	#[Test]
	public function testDeactivateAlreadyDeactivatedUserThrowsException(): void
	{
		$this->user->activate();
		$this->user->deactivate();
		$this->expectException(UserAlreadyDeactivatedException::class);
		$this->user->deactivate();
	}

	/**
	 * Tests deactivating a non-active user will throw an exception.
	 */
	#[Test]
	public function testDeactivateNonActiveUserThrowsException(): void
	{
		$this->expectException(UserCannotBeDeactivatedException::class);
		$this->user->deactivate();
	}

	/**
	 * Tests changing the email address.
	 */
	#[Test]
	public function testChangeEmail(): void
	{
		$newEmail = UserEmail::fromString('newemail@example.com');

		$this->user->changeEmail($newEmail);
		$this->assertSame('newemail@example.com', $this->user->getEmail()->toNative());

		$events = $this->user->getDomainEvents();

		$this->assertCount(2, $events);
		$this->assertInstanceOf(UserEmailWasChanged::class, $events[1]);
	}

	/**
	 * Tests changing the password.
	 */
	#[Test]
	public function testChangePassword(): void
	{
		$newPassword = PasswordHash::fromHash($this->passwordHasher->hash('new_password'));

		$this->user->changePassword($newPassword);
		$this->assertTrue($this->user->getPasswordHash()->equals($newPassword));

		$events = $this->user->getDomainEvents();

		$this->assertCount(2, $events);
		$this->assertInstanceOf(UserPasswordWasChanged::class, $events[1]);
	}

	#[Test]
	public function testGetDisplayName(): void
	{
		$this->assertSame('Test User', $this->user->getDisplayName()->toNative());
	}

	#[Test]
	public function testChangeDisplayName(): void
	{
		$newDisplayName = DisplayName::fromString('New Display Name');

		$this->user->changeDisplayName($newDisplayName);
		$this->assertTrue($this->user->getDisplayName()->equals($newDisplayName));

		$events = $this->user->getDomainEvents();

		$this->assertCount(2, $events);
		$this->assertInstanceOf(UserDisplayNameWasChanged::class, $events[1]);
	}

	#[Test]
	public function testChangeDisplayNameWithSameNameDoesNotRecordEvent(): void
	{
		$events = $this->user->getDomainEvents();
		$this->assertCount(1, $events);

		$this->user->changeDisplayName(DisplayName::fromString('Test User'));

		$events = $this->user->getDomainEvents();
		$this->assertCount(1, $events);
	}
}
