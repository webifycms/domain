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

namespace Webify\User\Identity\Domain\Entity;

use Webify\Base\Domain\Entity\AggregateRoot;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Identity\Domain\Event\{
	UserEmailWasChanged,
	UserPasswordWasChanged,
	UserWasActivated,
	UserWasDeactivated,
	UserWasRegistered
};
use Webify\User\Identity\Domain\Exception\{UserAlreadyActivateException, UserAlreadyDeactivatedException};
use Webify\User\Identity\Domain\ValueObject\{HashedPassword, UserEmail, UserId, UserStatus};

/**
 * User aggregate root.
 */
final class User extends AggregateRoot
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this aggregate root.
	 *
	 * @param UserId         $id          the unique identifier for the user
	 * @param UserEmail      $email       the email address of the user
	 * @param HashedPassword $password    the hashed password of the user
	 * @param string         $displayName the display name of the user
	 * @param DateTime       $createdAt   the datetime when the user was created
	 * @param DateTime       $updatedAt   the datetime when the user was last updated
	 * @param UserStatus     $status      the status indicating the user state
	 */
	private function __construct(
		private UserId $id,
		private UserEmail $email,
		private HashedPassword $password,
		private string $displayName,
		private DateTime $createdAt,
		private DateTime $updatedAt,
		private UserStatus $status
	) {}

	/**
	 * Get the ID.
	 */
	public function getId(): UserId
	{
		return $this->id;
	}

	/**
	 * Get the email.
	 */
	public function getEmail(): UserEmail
	{
		return $this->email;
	}

	/**
	 * Get the password hash.
	 */
	public function getPassword(): HashedPassword
	{
		return $this->password;
	}

	/**
	 * Get the display name.
	 */
	public function getDisplayName(): string
	{
		return $this->displayName;
	}

	/**
	 * Get the creation datetime.
	 */
	public function getCreatedAt(): DateTime
	{
		return $this->createdAt;
	}

	/**
	 * Get the updated datetime.
	 */
	public function getUpdatedAt(): DateTime
	{
		return $this->updatedAt;
	}

	/**
	 * Check if the user is active.
	 */
	public function getStatus(): UserStatus
	{
		return $this->status;
	}

	/**
	 * Activate the user.
	 *
	 * @throws UserAlreadyActivateException if the user is already active
	 */
	public function activate(): void
	{
		if ($this->status->isActive()) {
			throw new UserAlreadyActivateException();
		}

		$this->status    = UserStatus::Active;
		$this->updatedAt = DateTime::now();

		$this->recordDomainEvent(
			new UserWasActivated(
				$this->id->toNative(),
				$this->email->toNative(),
				$this->updatedAt
			)
		);
	}

	/**
	 * Deactivate the user.
	 *
	 * @throws UserAlreadyDeactivatedException if the user is already deactivated
	 */
	public function deactivate(): void
	{
		if ($this->status->isDeactivated()) {
			throw new UserAlreadyDeactivatedException();
		}

		$this->status    = UserStatus::Deactivated;
		$this->updatedAt = DateTime::now();

		$this->recordDomainEvent(
			new UserWasDeactivated(
				$this->id->toNative(),
				$this->email->toNative(),
				$this->updatedAt
			)
		);
	}

	/**
	 * Change the email address of the user.
	 */
	public function changeEmail(UserEmail $email): void
	{
		$oldEmail        = $this->email;
		$this->email     = $email;
		$this->updatedAt = DateTime::now();

		$this->recordDomainEvent(
			new UserEmailWasChanged(
				$this->id->toNative(),
				$oldEmail->toNative(),
				$email->toNative(),
				$this->updatedAt
			)
		);
	}

	/**
	 * Change the password of the user.
	 */
	public function changePassword(HashedPassword $password): void
	{
		$oldPassword     = $this->password;
		$this->password  = $password;
		$this->updatedAt = DateTime::now();

		$this->recordDomainEvent(
			new UserPasswordWasChanged(
				$this->id->toNative(),
				$oldPassword->toNative(),
				$password->toNative(),
				$this->updatedAt
			)
		);
	}

	/**
	 * Register a new user.
	 */
	public static function register(
		UserId $id,
		UserEmail $email,
		HashedPassword $password,
		string $displayName
	): self {
		$user = new self(
			$id,
			$email,
			$password,
			$displayName,
			DateTime::now(),
			DateTime::now(),
			UserStatus::Inactive
		);

		$user->recordDomainEvent(
			new UserWasRegistered(
				$user->getId()->toNative(),
				$user->getEmail()->toNative(),
				$user->getCreatedAt()
			)
		);

		return $user;
	}
}
