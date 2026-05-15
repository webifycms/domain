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

namespace Webify\Test\User\Authentication\Domain\Service;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Exception\SessionNotFoundException;
use Webify\User\Authentication\Domain\Repository\SessionRepositoryInterface;
use Webify\User\Authentication\Domain\Service\RevokeSession;
use Webify\User\Authentication\Domain\ValueObject\{AccessToken, RefreshToken, SessionId, UserId};

/**
 * RevokeSessionTest tests the functionality of the RevokeSession service.
 *
 * @internal
 */
#[CoversClass(RevokeSession::class)]
#[CoversMethod(RevokeSession::class, 'revoke')]
final class RevokeSessionTest extends TestCase
{
	/**
	 * The identifier for a specific session.
	 */
	private SessionId $sessionId;

	/**
	 * The unique identifier for a specific user.
	 */
	private UserId $userId;

	/**
	 * The access token for the session.
	 */
	private AccessToken $accessToken;

	/**
	 * The refresh token for the session.
	 */
	private RefreshToken $refreshToken;

	/**
	 * The expiration date of the session.
	 */
	private DateTime $expiresAt;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->sessionId    = SessionId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$this->userId       = UserId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAW');
		$this->accessToken  = AccessToken::generate();
		$this->refreshToken = RefreshToken::generate();
		$this->expiresAt    = DateTime::fromString('2099-01-01 00:00:00');
	}

	/**
	 * Tests that a session can be revoked successfully.
	 *
	 * This method verifies the full revocation flow:
	 * - The session is retrieved from the repository using the provided session ID.
	 * - The session is marked as revoked.
	 * - The updated session is persisted back to the repository.
	 * - A SessionWasRevoked domain event is published.
	 */
	#[Test]
	public function testRevokeSessionSuccessfully(): void
	{
		$session = Session::open(
			$this->sessionId,
			$this->userId,
			$this->accessToken,
			$this->refreshToken,
			$this->expiresAt
		);
		$repository = $this->createMock(SessionRepositoryInterface::class);

		$repository->method('getById')
			->willReturn($session)
		;
		$repository->expects($this->once())
			->method('persist')
			->with($session)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->once())
			->method('publish')
		;

		$service = new RevokeSession($repository, $eventPublisher);

		$service->revoke($this->sessionId->toNative());
	}

	/**
	 * Tests that an exception is thrown when attempting to revoke a non-existent session.
	 *
	 * Verifies that the SessionNotFoundException propagates from the repository
	 * and that persist and publish are never called.
	 */
	#[Test]
	public function testRevokeSessionThrowsExceptionWhenSessionNotFound(): void
	{
		$repository = $this->createMock(SessionRepositoryInterface::class);

		$repository->method('getById')
			->willThrowException(SessionNotFoundException::forId('01ARZ3NDEKTSV4RRFFQ69G5FAV'))
		;
		$repository->expects($this->never())
			->method('persist')
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->never())
			->method('publish')
		;

		$this->expectException(SessionNotFoundException::class);

		$service = new RevokeSession($repository, $eventPublisher);

		$service->revoke('01ARZ3NDEKTSV4RRFFQ69G5FAV');
	}

	/**
	 * Tests that revoking an already revoked session is idempotent.
	 *
	 * Verifies that the service can be called multiple times with the same session
	 * without errors, relying on the entity-level idempotency of the revoke method.
	 */
	#[Test]
	public function testRevokeSessionIsIdempotent(): void
	{
		$session = Session::open(
			$this->sessionId,
			$this->userId,
			$this->accessToken,
			$this->refreshToken,
			$this->expiresAt
		);
		$repository = $this->createMock(SessionRepositoryInterface::class);

		$repository->method('getById')
			->willReturn($session)
		;
		$repository->expects($this->exactly(2))
			->method('persist')
			->with($session)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher->expects($this->exactly(2))
			->method('publish')
		;

		$service = new RevokeSession($repository, $eventPublisher);

		$service->revoke($this->sessionId->toNative());
		$service->revoke($this->sessionId->toNative());
	}
}
