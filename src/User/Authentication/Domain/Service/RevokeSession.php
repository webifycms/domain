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

use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\User\Authentication\Domain\Repository\SessionRepositoryInterface;
use Webify\User\Authentication\Domain\ValueObject\SessionId;

/**
 * RevokeSession domain service handles the revoking of the session.
 *
 * Encapsulate the use case of explicitly invalidating an authentication session (logout).
 * The service is idempotent and silent when the session does not exist.
 */
final readonly class RevokeSession
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private SessionRepositoryInterface $repository,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Revokes a session identified by the provided session ID.
	 *
	 * @param string $sessionId the unique identifier of the session to be revoked
	 */
	public function revoke(string $sessionId): void
	{
		// If session is not found, throw an exception itself
		$session = $this->repository->getById(SessionId::fromString($sessionId));

		$session->revoke();
		$this->repository->persist($session);
		$this->eventPublisher->publish(...$session->getDomainEvents());
	}
}
