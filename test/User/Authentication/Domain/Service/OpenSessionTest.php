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
use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Repository\SessionRepositoryInterface;
use Webify\User\Authentication\Domain\Service\OpenSession;
use Webify\User\Authentication\Domain\ValueObject\{AuthenticatedUser, UserId};

/**
 * Tests for the OpenSession domain service.
 *
 * @internal
 */
#[CoversClass(OpenSession::class)]
#[CoversMethod(OpenSession::class, 'open')]
final class OpenSessionTest extends TestCase
{
	private const string USER_ID = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

	private const string SESSION_ID = '01ARZ3NDEKTSV4RRFFQ69G5FAW';

	/**
	 * Test successfully opening a session for an authenticated user.
	 */
	#[Test]
	public function testOpenSessionSuccessfully(): void
	{
		$expiresAt         = new DateTimeImmutable('2099-01-01 00:00:00');
		$userId            = UserId::fromString(self::USER_ID);
		$authenticatedUser = AuthenticatedUser::fromSuccessfulAuthentication($userId);

		$repository = $this->createMock(SessionRepositoryInterface::class);
		$repository
			->expects($this->once())
			->method('persist')
			->with($this->isInstanceOf(Session::class))
		;

		$idGenerator = $this->createMock(UlidGeneratorInterface::class);
		$idGenerator
			->expects($this->once())
			->method('generate')
			->willReturn(self::SESSION_ID)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);
		$eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$service = new OpenSession($repository, $idGenerator, $eventPublisher);
		$session = $service->open($authenticatedUser, $expiresAt);

		$this->assertInstanceOf(Session::class, $session);
		$this->assertTrue($session->getUserId()->equals($userId));
	}

	/**
	 * Test that the opened session has the correct initial state.
	 */
	#[Test]
	public function testOpenSessionInitialState(): void
	{
		$expiresAt         = new DateTimeImmutable('2099-01-01 00:00:00');
		$userId            = UserId::fromString(self::USER_ID);
		$authenticatedUser = AuthenticatedUser::fromSuccessfulAuthentication($userId);

		$repository = $this->createMock(SessionRepositoryInterface::class);
		$repository
			->expects($this->once())
			->method('persist')
		;

		$idGenerator = $this->createMock(UlidGeneratorInterface::class);
		$idGenerator
			->expects($this->once())
			->method('generate')
			->willReturn(self::SESSION_ID)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);
		$eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$service = new OpenSession($repository, $idGenerator, $eventPublisher);
		$session = $service->open($authenticatedUser, $expiresAt);

		$this->assertFalse($session->isExpired());
		$this->assertFalse($session->isRevoked());
		$this->assertTrue($session->isActive());
	}
}
