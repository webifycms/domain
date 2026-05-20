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

namespace Webify\User\Identity\Domain\Service;

use Webify\Base\Domain\Contract\Identity\PasswordHasherInterface;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\User\Identity\Domain\Exception\CurrentPasswordNotMatchedException;
use Webify\User\Identity\Domain\Guard\PasswordMustBeStrong;
use Webify\User\Identity\Domain\Repository\UserRepositoryInterface;
use Webify\User\Identity\Domain\ValueObject\{PasswordHash, UserId};

/**
 * UpdateUserPassword service handles the user password update process.
 *
 * Encapsulate the use case of changing a user's password. Verifies the current password
 * before updating, then hashes the new password, and delegates to the aggregate.
 */
final readonly class UpdateUserPassword
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private PasswordHasherInterface $passwordHasher,
		private UserRepositoryInterface $repository,
		private PasswordMustBeStrong $passwordMustBeStrong,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Updates a user's password after verifying the current password.
	 *
	 * @param string $userId          the unique identifier of the user
	 * @param string $currentPassword the user's current password for verification
	 * @param string $newPassword     the new password to be set for the user
	 *
	 * @throws CurrentPasswordNotMatchedException if the current password does not match the stored one
	 */
	public function update(string $userId, string $currentPassword, string $newPassword): void
	{
		$user = $this->repository->getById(UserId::fromString($userId));

		if (!$this->passwordHasher->verify($currentPassword, $user->getPasswordHash()->toNative())) {
			throw CurrentPasswordNotMatchedException::create();
		}

		$this->passwordMustBeStrong->guard($newPassword);

		$passwordHash = PasswordHash::fromHash($this->passwordHasher->hash($newPassword));

		$user->changePassword($passwordHash);
		$this->repository->persist($user);
		$this->eventPublisher->publish(...$user->getDomainEvents());
	}
}
