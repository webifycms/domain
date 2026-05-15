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
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Repository\SessionRepositoryInterface;
use Webify\User\Authentication\Domain\ValueObject\{AccessToken, RefreshToken};

/**
 * RefreshSession domain service handles the refreshing of the session.
 *
 * Encapsulate the use case of exchanging a valid RefreshToken for a new token pair, extending
 * the session. The old tokens are invalidated atomically by replacing them inside the same
 * AuthSession aggregate.
 */
final readonly class RefreshSession
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private SessionRepositoryInterface $repository,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Refreshes the session by generating a new access token and refresh token, then updates the session
	 * with the provided expiration date.
	 *
	 * @param string            $token     the refresh token used to identify the session
	 * @param DateTimeImmutable $expiresAt the new expiration date for the session
	 */
	public function refresh(string $token, DateTimeImmutable $expiresAt): void
	{
		// If session is not found, throw an exception itself
		$session = $this->repository->getByRefreshToken(RefreshToken::fromString($token));

		// If the session is expired or revoked, throw an exception itself
		$session->refresh(AccessToken::generate(), RefreshToken::generate(), DateTime::fromNative($expiresAt));
		$this->repository->persist($session);
		$this->eventPublisher->publish(...$session->getDomainEvents());
	}
}
