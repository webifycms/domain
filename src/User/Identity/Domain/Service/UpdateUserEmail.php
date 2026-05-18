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

use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\User\Identity\Domain\Guard\EmailMustBeUnique;
use Webify\User\Identity\Domain\Repository\UserRepositoryInterface;
use Webify\User\Identity\Domain\ValueObject\{UserEmail, UserId};

/**
 * UpdateUserEmail service handles the user email update process.
 *
 * Encapsulate the use case of changing a user's email address. Validates that the new email
 * is not already taken before updating.
 */
final readonly class UpdateUserEmail
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private EmailMustBeUnique $emailMustBeUnique,
		private UserRepositoryInterface $repository,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Updates a user's password after verifying the current password.
	 *
	 * @param string $userId       the unique identifier of the user
	 * @param string $newUserEmail the user's new email for change
	 */
	public function update(string $userId, string $newUserEmail): void
	{
		$user  = $this->repository->getById(UserId::fromString($userId));
		$email = UserEmail::fromString($newUserEmail);

		$this->emailMustBeUnique->guard($email);

		$user->changeEmail($email);
		$this->repository->persist($user);
		$this->eventPublisher->publish(...$user->getDomainEvents());
	}
}
