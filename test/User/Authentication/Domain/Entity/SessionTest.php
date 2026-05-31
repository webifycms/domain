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

namespace Webify\Test\User\Authentication\Domain\Entity;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Event\{SessionWasOpened, SessionWasRefreshed, SessionWasRevoked};
use Webify\User\Authentication\Domain\Exception\CannotRefreshSessionException;
use Webify\User\Authentication\Domain\ValueObject\{AccessToken, RefreshToken, SessionId, UserId};

/**
 * Tests for the Session entity.
 *
 * @internal
 */
#[CoversClass(Session::class)]
#[CoversMethod(Session::class, 'open')]
#[CoversMethod(Session::class, 'getId')]
#[CoversMethod(Session::class, 'getUserId')]
#[CoversMethod(Session::class, 'getAccessToken')]
#[CoversMethod(Session::class, 'getRefreshToken')]
#[CoversMethod(Session::class, 'getExpiresAt')]
#[CoversMethod(Session::class, 'getCreatedAt')]
#[CoversMethod(Session::class, 'refresh')]
#[CoversMethod(Session::class, 'revoke')]
#[CoversMethod(Session::class, 'isExpired')]
#[CoversMethod(Session::class, 'isActive')]
#[CoversMethod(Session::class, 'isRevoked')]
final class SessionTest extends TestCase
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
	public function testOpenSessionRecordsSessionWasOpenedEvent(): void
	{
		$session = $this->createSession();
		$events  = $session->getDomainEvents();

		$this->assertCount(1, $events);
		$this->assertInstanceOf(SessionWasOpened::class, $events[0]);
	}

	#[Test]
	public function testOpenSessionInitialState(): void
	{
		$session = $this->createSession();

		$this->assertFalse($session->isExpired());
		$this->assertFalse($session->isRevoked());
		$this->assertTrue($session->isActive());
	}

	#[Test]
	public function testSessionGetters(): void
	{
		$session = $this->createSession();

		$this->assertTrue($this->sessionId->equals($session->getId()));
		$this->assertTrue($this->userId->equals($session->getUserId()));
		$this->assertTrue($this->accessToken->equals($session->getAccessToken()));
		$this->assertTrue($this->refreshToken->equals($session->getRefreshToken()));
		$this->assertTrue($this->expiresAt->equals($session->getExpiresAt()));
		$this->assertInstanceOf(DateTime::class, $session->getCreatedAt());
	}

	#[Test]
	public function testRefreshSession(): void
	{
		$session         = $this->createSession();
		$newAccessToken  = AccessToken::generate();
		$newRefreshToken = RefreshToken::generate();
		$newExpiresAt    = DateTime::fromString('2099-06-01 00:00:00');

		$session->refresh($newAccessToken, $newRefreshToken, $newExpiresAt);

		$this->assertTrue($newAccessToken->equals($session->getAccessToken()));
		$this->assertTrue($newRefreshToken->equals($session->getRefreshToken()));
		$this->assertTrue($newExpiresAt->equals($session->getExpiresAt()));

		$events = $session->getDomainEvents();

		$this->assertCount(2, $events);
		$this->assertInstanceOf(SessionWasRefreshed::class, $events[1]);
	}

	#[Test]
	public function testRefreshExpiredSessionThrowsException(): void
	{
		$session = Session::open(
			$this->sessionId,
			$this->userId,
			$this->accessToken,
			$this->refreshToken,
			DateTime::fromString('2020-01-01 00:00:00')
		);

		$this->expectException(CannotRefreshSessionException::class);
		$session->refresh(
			AccessToken::generate(),
			RefreshToken::generate(),
			DateTime::fromString('2099-01-01 00:00:00')
		);
	}

	#[Test]
	public function testRefreshRevokedSessionThrowsException(): void
	{
		$session = $this->createSession();

		$session->revoke();
		$session->releaseDomainEvents();
		$this->expectException(CannotRefreshSessionException::class);
		$session->refresh(
			AccessToken::generate(),
			RefreshToken::generate(),
			DateTime::fromString('2099-06-01 00:00:00')
		);
	}

	#[Test]
	public function testRevokeSession(): void
	{
		$session = $this->createSession();

		$session->revoke();
		$this->assertTrue($session->isRevoked());
		$this->assertFalse($session->isActive());

		$events = $session->getDomainEvents();

		$this->assertCount(2, $events);
		$this->assertInstanceOf(SessionWasRevoked::class, $events[1]);
	}

	#[Test]
	public function testRevokeSessionIsIdempotent(): void
	{
		$session = $this->createSession();

		$session->revoke();
		$session->revoke();

		$revokeEvents = array_values(
			array_filter(
				$session->getDomainEvents(),
				static fn (object $event): bool => $event instanceof SessionWasRevoked
			)
		);

		$this->assertCount(1, $revokeEvents);
	}

	#[Test]
	public function testIsExpiredReturnsTrueForExpiredSession(): void
	{
		$session = Session::open(
			$this->sessionId,
			$this->userId,
			$this->accessToken,
			$this->refreshToken,
			DateTime::fromString('2020-01-01 00:00:00')
		);

		$this->assertTrue($session->isExpired());
	}

	#[Test]
	public function testIsExpiredReturnsFalseForNonExpiredSession(): void
	{
		$session = $this->createSession();

		$this->assertFalse($session->isExpired());
	}

	#[Test]
	public function testIsActiveReturnsFalseForExpiredSession(): void
	{
		$session = Session::open(
			$this->sessionId,
			$this->userId,
			$this->accessToken,
			$this->refreshToken,
			DateTime::fromString('2020-01-01 00:00:00')
		);

		$this->assertFalse($session->isActive());
	}

	#[Test]
	public function testIsActiveReturnsFalseForRevokedSession(): void
	{
		$session = $this->createSession();

		$session->revoke();
		$this->assertFalse($session->isActive());
	}

	private function createSession(): Session
	{
		return Session::open(
			$this->sessionId,
			$this->userId,
			$this->accessToken,
			$this->refreshToken,
			$this->expiresAt
		);
	}
}
