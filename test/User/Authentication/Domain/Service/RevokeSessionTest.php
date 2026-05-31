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
 * Tests for the RevokeSession service.
 *
 * @internal
 */
#[CoversClass(RevokeSession::class)]
#[CoversMethod(RevokeSession::class, 'revoke')]
final class RevokeSessionTest extends TestCase
{
	private SessionId $sessionId;

	private UserId $userId;

	private AccessToken $accessToken;

	private RefreshToken $refreshToken;

	private DateTime $expiresAt;

	protected function setUp(): void
	{
		$this->sessionId    = SessionId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$this->userId       = UserId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAW');
		$this->accessToken  = AccessToken::generate();
		$this->refreshToken = RefreshToken::generate();
		$this->expiresAt    = DateTime::fromString('2099-01-01 00:00:00');
	}

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
