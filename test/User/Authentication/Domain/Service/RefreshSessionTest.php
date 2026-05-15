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

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\{AllowMockObjectsWithoutExpectations, CoversClass, CoversMethod, Test};
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Exception\{CannotRefreshSessionException, SessionNotFoundException};
use Webify\User\Authentication\Domain\Repository\SessionRepositoryInterface;
use Webify\User\Authentication\Domain\Service\RefreshSession;
use Webify\User\Authentication\Domain\ValueObject\{AccessToken, RefreshToken, SessionId, UserId};

/**
 * RefreshSessionTest tests the RefreshSession domain service.
 *
 * @internal
 */
#[CoversClass(RefreshSession::class)]
#[CoversMethod(RefreshSession::class, 'refresh')]
final class RefreshSessionTest extends TestCase
{
	/**
	 * Repository instance.
	 */
	private MockObject&SessionRepositoryInterface $repository;

	/**
	 * Domain event publisher service instance.
	 */
	private DomainEventPublisherInterface&MockObject $eventPublisher;

	/**
	 * The RefreshSession instance.
	 */
	private RefreshSession $refreshSession;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->repository     = $this->createMock(SessionRepositoryInterface::class);
		$this->eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$this->refreshSession = new RefreshSession(
			$this->repository,
			$this->eventPublisher
		);
	}

	/**
	 * Tests that the session is successfully refreshed when a valid refresh token is provided.
	 *
	 * This method verifies that the refresh process is executed correctly by:
	 * - Fetching the session using the provided refresh token.
	 * - Invoking the refresh method on the session to rotate tokens and extend expiry.
	 * - Persisting the updated session in the repository.
	 * - Publishing the domain event for the refreshed session.
	 *
	 * It asserts that:
	 * - The session's access and refresh tokens are rotated after the refresh.
	 * - The session's expiration date is updated to the new expiry.
	 * - The repository persist method is called with the updated session.
	 */
	#[Test]
	public function testRefreshSessionSuccessfully(): void
	{
		$token          = '4500ccbd09e402fe83717c67dd156512837bfc3fd244125c430260ddd8816266';
		$expiresAt      = new DateTimeImmutable('2099-01-01 00:00:00');
		$originalAccess = AccessToken::generate();
		$session        = Session::open(
			SessionId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			UserId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAW'),
			$originalAccess,
			RefreshToken::fromString($token),
			DateTime::fromString('2099-06-01 00:00:00')
		);

		$this->repository
			->expects($this->once())
			->method('getByRefreshToken')
			->with($this->isInstanceOf(RefreshToken::class))
			->willReturn($session)
		;
		$this->repository
			->expects($this->once())
			->method('persist')
			->with($session)
		;
		$this->eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$this->refreshSession->refresh($token, $expiresAt);

		$this->assertFalse($originalAccess->equals($session->getAccessToken()));
		$this->assertTrue($session->getExpiresAt()->equals(DateTime::fromNative($expiresAt)));
	}

	/**
	 * Tests that the refresh method throws a SessionNotFoundException when the refresh token is not recognized.
	 */
	#[Test]
	#[AllowMockObjectsWithoutExpectations]
	public function testRefreshThrowsExceptionWhenTokenNotFound(): void
	{
		$token = '4500ccbd09e402fe83717c67dd156512837bfc3fd244125c430260ddd8816266';

		$this->repository
			->expects($this->once())
			->method('getByRefreshToken')
			->with($this->isInstanceOf(RefreshToken::class))
			->willThrowException(SessionNotFoundException::forRefreshToken($token))
		;
		$this->expectException(SessionNotFoundException::class);
		$this->refreshSession->refresh($token, new DateTimeImmutable('2099-01-01 00:00:00'));
	}

	/**
	 * Tests that the refresh method throws a CannotRefreshSessionException when the session
	 * is expired or has been revoked.
	 */
	#[Test]
	#[AllowMockObjectsWithoutExpectations]
	public function testRefreshThrowsExceptionWhenSessionCannotBeRefreshed(): void
	{
		$token   = '4500ccbd09e402fe83717c67dd156512837bfc3fd244125c430260ddd8816266';
		$session = Session::open(
			SessionId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			UserId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAW'),
			AccessToken::generate(),
			RefreshToken::fromString($token),
			DateTime::fromString('2020-01-01 00:00:00')
		);

		$this->repository
			->expects($this->once())
			->method('getByRefreshToken')
			->with($this->isInstanceOf(RefreshToken::class))
			->willReturn($session)
		;
		$this->expectException(CannotRefreshSessionException::class);
		$this->refreshSession->refresh($token, new DateTimeImmutable('2099-01-01 00:00:00'));
	}
}
