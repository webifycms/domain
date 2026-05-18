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
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\User\Identity\Domain\Entity\User;
use Webify\User\Identity\Domain\Event\UserEmailWasChanged;
use Webify\User\Identity\Domain\Exception\EmailMustBeUniqueException;
use Webify\User\Identity\Domain\Guard\EmailMustBeUnique;
use Webify\User\Identity\Domain\Repository\UserRepositoryInterface;
use Webify\User\Identity\Domain\Service\UpdateUserEmail;
use Webify\User\Identity\Domain\ValueObject\{DisplayName, PasswordHash, UserEmail, UserId};

/**
 * UpdateUserEmailTest tests the UpdateUserEmail domain service.
 *
 * @internal
 */
#[CoversClass(UpdateUserEmail::class)]
#[CoversMethod(UpdateUserEmail::class, 'update')]
final class UpdateUserEmailTest extends TestCase
{
	/**
	 * Repository instance.
	 */
	private MockObject&UserRepositoryInterface $repository;

	/**
	 * Domain event publisher service instance.
	 */
	private DomainEventPublisherInterface&MockObject $eventPublisher;

	/**
	 * The UpdateUserEmail instance.
	 */
	private UpdateUserEmail $updateUserEmail;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->repository      = $this->createMock(UserRepositoryInterface::class);
		$this->eventPublisher  = $this->createMock(DomainEventPublisherInterface::class);
		$this->updateUserEmail = new UpdateUserEmail(
			new EmailMustBeUnique($this->repository),
			$this->repository,
			$this->eventPublisher
		);
	}

	/**
	 * Tests the successful update of a user's email address.
	 *
	 * This method verifies the full email update flow:
	 * - The user is retrieved from the repository using the provided user ID.
	 * - The new email is checked for uniqueness before the aggregate is changed.
	 * - The updated user is persisted back to the repository.
	 * - A UserEmailWasChanged domain event is published with the old and new email addresses.
	 */
	#[Test]
	public function testUpdateUserEmailSuccessfully(): void
	{
		$userId       = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$currentEmail = 'test@example.com';
		$newEmail     = 'newemail@example.com';
		$user         = $this->createUser($userId, $currentEmail);

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
		$this->repository
			->expects($this->once())
			->method('isExists')
			->with($this->callback(
				function (UserEmail $providedEmail) use ($newEmail): bool {
					return $providedEmail->toNative() === $newEmail;
				}
			))
			->willReturn(false)
		;
		$this->repository
			->expects($this->once())
			->method('persist')
			->with($this->callback(
				function (User $persistedUser) use ($user, $newEmail): bool {
					$this->assertSame($user, $persistedUser);
					$this->assertSame($newEmail, $persistedUser->getEmail()->toNative());

					return true;
				}
			))
		;
		$this->eventPublisher
			->expects($this->once())
			->method('publish')
			->with($this->callback(
				function (UserEmailWasChanged $event) use ($userId, $currentEmail, $newEmail): bool {
					$this->assertSame($userId, $event->userId);
					$this->assertSame($currentEmail, $event->oldEmail);
					$this->assertSame($newEmail, $event->newEmail);

					return true;
				}
			))
		;
		$this->updateUserEmail->update($userId, $newEmail);
	}

	/**
	 * Tests that the update method throws an exception when the new email already exists.
	 *
	 * The method verifies that the EmailMustBeUniqueException is raised before
	 * persisting the user or publishing domain events.
	 */
	#[Test]
	public function testUpdateThrowsExceptionWhenEmailAlreadyExists(): void
	{
		$userId      = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$newEmail    = 'existing@example.com';
		$user        = $this->createUser($userId, 'test@example.com');

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
		$this->repository
			->expects($this->once())
			->method('isExists')
			->with($this->callback(
				function (UserEmail $providedEmail) use ($newEmail): bool {
					return $providedEmail->toNative() === $newEmail;
				}
			))
			->willReturn(true)
		;
		$this->repository
			->expects($this->never())
			->method('persist')
		;
		$this->eventPublisher
			->expects($this->never())
			->method('publish')
		;
		$this->expectException(EmailMustBeUniqueException::class);
		$this->updateUserEmail->update($userId, $newEmail);
	}

	/**
	 * Creates a user aggregate for email update tests.
	 *
	 * The registration event is released to represent a user already
	 * loaded from persistence before the email update use case starts.
	 */
	private function createUser(string $userId, string $email): User
	{
		$user = User::register(
			UserId::fromString($userId),
			UserEmail::fromString($email),
			PasswordHash::fromHash('password_hash'),
			DisplayName::fromString('Test User')
		);

		$user->releaseDomainEvents();

		return $user;
	}
}
