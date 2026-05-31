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
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Repository\SessionRepositoryInterface;
use Webify\User\Authentication\Domain\ValueObject\{AccessToken, AuthenticatedUser, RefreshToken, SessionId};

/**
 * OpenSession domain service handles the opening of a new session.
 *
 * Encapsulate the use case of opening a session for an authenticated user and persisting it to the repository.
 *
 * @internal this class is not intended for public use, it should only be used within the Authentication strategies
 */
final readonly class OpenSession
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private SessionRepositoryInterface $repository,
		private UlidGeneratorInterface $idGenerator,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Opens a new session for a user after validating their credentials.
	 *
	 * @param AuthenticatedUser $authenticatedUser the authenticated user
	 * @param DateTimeImmutable $expiresAt         the expiration time for the session
	 */
	public function open(
		AuthenticatedUser $authenticatedUser,
		DateTimeImmutable $expiresAt
	): Session {
		$session = Session::open(
			SessionId::fromString($this->idGenerator->generate()),
			$authenticatedUser->getUserId(),
			AccessToken::generate(),
			RefreshToken::generate(),
			DateTime::fromNative($expiresAt)
		);

		$this->repository->persist($session);
		$this->eventPublisher->publish(...$session->getDomainEvents());

		return $session;
	}
}
