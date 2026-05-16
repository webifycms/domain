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
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\User\Identity\Domain\Entity\User;
use Webify\User\Identity\Domain\Guard\EmailMustBeUnique;
use Webify\User\Identity\Domain\Repository\UserRepositoryInterface;
use Webify\User\Identity\Domain\ValueObject\{DisplayName, PasswordHash, UserEmail, UserId};

/**
 * RegisterUser service handles the user registering process.
 *
 * Encapsulate the use case of registering a new user. Orchestrates email uniqueness validation,
 * password hashing, user entity creation, persistence, and event publishing.
 */
final readonly class RegisterUser
{
	public function __construct(
		private PasswordHasherInterface $passwordHasher,
		private UserRepositoryInterface $repository,
		private EmailMustBeUnique $emailMustBeUnique,
		private UlidGeneratorInterface $ulidGenerator,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Registers a new user with the provided email and password.
	 *
	 * @param string $email       the email of the user to register
	 * @param string $password    the password of the user to register
	 * @param string $displayName the display name of the user to register
	 */
	public function register(string $email, string $password, string $displayName): void
	{
		$userEmail = UserEmail::fromString($email);

		$this->emailMustBeUnique->guard($userEmail);

		$hashedPassword = $this->passwordHasher->hash($password);
		$user           = User::register(
			UserId::fromString($this->ulidGenerator->generate()),
			$userEmail,
			PasswordHash::fromHash($hashedPassword),
			DisplayName::fromString($displayName)
		);

		$this->repository->persist($user);
		$this->eventPublisher->publish(...$user->getDomainEvents());
	}
}
