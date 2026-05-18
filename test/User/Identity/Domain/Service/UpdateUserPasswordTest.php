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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Contract\Identity\PasswordHasherInterface;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\User\Identity\Domain\Entity\User;
use Webify\User\Identity\Domain\Event\UserPasswordWasChanged;
use Webify\User\Identity\Domain\Exception\CurrentPasswordNotMatchedException;
use Webify\User\Identity\Domain\Repository\UserRepositoryInterface;
use Webify\User\Identity\Domain\Service\UpdateUserPassword;
use Webify\User\Identity\Domain\ValueObject\{DisplayName, PasswordHash, UserEmail, UserId};

/**
 * UpdateUserPasswordTest tests the UpdateUserPassword domain service.
 *
 * @internal
 */
#[CoversClass(UpdateUserPassword::class)]
#[CoversMethod(UpdateUserPassword::class, 'update')]
final class UpdateUserPasswordTest extends TestCase
{
	/**
	 * Password hasher service instance.
	 */
	private MockObject&PasswordHasherInterface $passwordHasher;

	/**
	 * Repository instance.
	 */
	private MockObject&UserRepositoryInterface $repository;

	/**
	 * Domain event publisher service instance.
	 */
	private DomainEventPublisherInterface&MockObject $eventPublisher;

	/**
	 * The UpdateUserPassword instance.
	 */
	private UpdateUserPassword $updateUserPassword;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->passwordHasher     = $this->createMock(PasswordHasherInterface::class);
		$this->repository         = $this->createMock(UserRepositoryInterface::class);
		$this->eventPublisher     = $this->createMock(DomainEventPublisherInterface::class);
		$this->updateUserPassword = new UpdateUserPassword(
			$this->passwordHasher,
			$this->repository,
			$this->eventPublisher
		);
	}

	/**
	 * Tests the successful update of a user's password.
	 *
	 * This method verifies the full password update flow:
	 * - The user is retrieved from the repository using the provided user ID.
	 * - The current password is verified against the stored password hash.
	 * - The new password is hashed and applied to the user aggregate.
	 * - The updated user is persisted back to the repository.
	 * - A UserPasswordWasChanged domain event is published.
	 */
	#[Test]
	public function testUpdateUserPasswordSuccessfully(): void
	{
		$userId              = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$currentPassword     = 'current_password';
		$newPassword         = 'new_password';
		$currentPasswordHash = 'current_password_hash';
		$newPasswordHash     = 'new_password_hash';
		$user                = $this->createUser($userId, $currentPasswordHash);

		$this->repository
			->expects($this->once())
			->method('getById')
			->with($this->callback(
				function (UserId $providedUserId) use ($userId): bool {
					return $providedUserId->toNative() === $userId;
				}
			))
			->willReturn($user)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('verify')
			->with($currentPassword, $currentPasswordHash)
			->willReturn(true)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('hash')
			->with($newPassword)
			->willReturn($newPasswordHash)
		;
		$this->repository
			->expects($this->once())
			->method('persist')
			->with($this->callback(
				function (User $persistedUser) use ($user, $newPasswordHash): bool {
					$this->assertSame($user, $persistedUser);
					$this->assertSame($newPasswordHash, $persistedUser->getPasswordHash()->toNative());

					return true;
				}
			))
		;
		$this->eventPublisher
			->expects($this->once())
			->method('publish')
			->with($this->isInstanceOf(UserPasswordWasChanged::class))
		;
		$this->updateUserPassword->update($userId, $currentPassword, $newPassword);
	}

	/**
	 * Tests that the update method throws an exception when the current password is invalid.
	 *
	 * The method verifies that the CurrentPasswordNotMatchedException is raised
	 * before hashing the new password, persisting the user, or publishing events.
	 */
	#[Test]
	public function testUpdateThrowsExceptionWhenCurrentPasswordIsInvalid(): void
	{
		$userId              = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$currentPassword     = 'wrong_current_password';
		$currentPasswordHash = 'current_password_hash';
		$user                = $this->createUser($userId, $currentPasswordHash);

		$this->repository
			->expects($this->once())
			->method('getById')
			->with($this->callback(
				function (UserId $providedUserId) use ($userId): bool {
					return $providedUserId->toNative() === $userId;
				}
			))
			->willReturn($user)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('verify')
			->with($currentPassword, $currentPasswordHash)
			->willReturn(false)
		;
		$this->passwordHasher
			->expects($this->never())
			->method('hash')
		;
		$this->repository
			->expects($this->never())
			->method('persist')
		;
		$this->eventPublisher
			->expects($this->never())
			->method('publish')
		;
		$this->expectException(CurrentPasswordNotMatchedException::class);
		$this->updateUserPassword->update($userId, $currentPassword, 'new_password');
	}

	/**
	 * Creates a user aggregate for password update tests.
	 *
	 * The registration event is released to represent a user already
	 * loaded from persistence before the password update use case starts.
	 */
	private function createUser(string $userId, string $passwordHash): User
	{
		$user = User::register(
			UserId::fromString($userId),
			UserEmail::fromString('test@example.com'),
			PasswordHash::fromHash($passwordHash),
			DisplayName::fromString('Test User')
		);

		$user->releaseDomainEvents();

		return $user;
	}
}
