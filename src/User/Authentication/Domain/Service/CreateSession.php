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

namespace Webify\User\Authentication\Domain\Service;

use DateTimeImmutable;
use Webify\Base\Domain\Contract\Authentication\UserCredentialLookupInterface;
use Webify\Base\Domain\Contract\Identity\PasswordHasherInterface;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Exception\{
	InvalidUserCredentialException,
	UserNotEligibleForAuthenticationException
};
use Webify\User\Authentication\Domain\Guard\UserMustBeActive;
use Webify\User\Authentication\Domain\Repository\SessionRepositoryInterface;
use Webify\User\Authentication\Domain\ValueObject\{AccessToken, RefreshToken, SessionId, UserId};

/**
 * CreateSession service handles the creation of a new session.
 *
 * Encapsulate the use case of authenticating a user with email and password and issuing a new
 * session. This is the primary entry point into the Authentication BC.
 *
 * The service verifies credentials, enforces the UserMustBeActive guard, generates token pairs,
 * and persists the new session.
 */
final readonly class CreateSession
{
	public function __construct(
		private UserCredentialLookupInterface $credentialLookup,
		private UserMustBeActive $userMustBeActive,
		private PasswordHasherInterface $passwordHasher,
		private SessionRepositoryInterface $repository,
		private UlidGeneratorInterface $idGenerator,
		private UserStatusTranslator $userStatusTranslator,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Creates a new session for a user after validating their credentials.
	 *
	 * @param string            $email     the email address of the user attempting to authenticate
	 * @param string            $password  the password provided for authentication
	 * @param DateTimeImmutable $expiresAt the expiration time for the session
	 *
	 * @return Session the newly created session instance
	 *
	 * @throws InvalidUserCredentialException            if the email or password is invalid
	 * @throws UserNotEligibleForAuthenticationException if the user's status does not allow session creation
	 */
	public function create(
		string $email,
		string $password,
		DateTimeImmutable $expiresAt
	): Session {
		$credential = $this->credentialLookup->findByEmail($email);

		if (null === $credential) {
			throw InvalidUserCredentialException::create();
		}

		if (!$this->passwordHasher->verify($password, $credential->passwordHash)) {
			throw InvalidUserCredentialException::create();
		}

		// Guard for checking user status
		$this->userMustBeActive->guard($this->userStatusTranslator->translate($credential->userStatus));

		$session = Session::open(
			SessionId::fromString($this->idGenerator->generate()),
			UserId::fromString($credential->id),
			AccessToken::generate(),
			RefreshToken::generate(),
			DateTime::fromNative($expiresAt)
		);

		$this->repository->persist($session);
		$this->eventPublisher->publish(...$session->getDomainEvents());

		return $session;
	}
}
