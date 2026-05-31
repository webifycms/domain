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

namespace Webify\Test\User\Authentication\Domain\Strategy;

use PHPUnit\Framework\Attributes\{AllowMockObjectsWithoutExpectations, CoversClass, CoversMethod, Test};
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Contract\Authentication\{Credentials, UserCredentials};
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\User\Authentication\Domain\Entity\Challenge;
use Webify\User\Authentication\Domain\Repository\ChallengeRepositoryInterface;
use Webify\User\Authentication\Domain\Service\{ChallengeSecretDeliveryInterface, IssueChallenge};
use Webify\User\Authentication\Domain\Strategy\ChallengeCodeStrategy;
use Webify\User\Authentication\Domain\ValueObject\UserId;

/**
 * Tests for the ChallengeCodeStrategy.
 *
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
#[CoversClass(ChallengeCodeStrategy::class)]
#[CoversMethod(ChallengeCodeStrategy::class, 'getIdentifier')]
#[CoversMethod(ChallengeCodeStrategy::class, 'isSupported')]
#[CoversMethod(ChallengeCodeStrategy::class, 'initiate')]
final class ChallengeCodeStrategyTest extends TestCase
{
	/**
	 * The user ID used for testing.
	 */
	private const string USER_ID = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

	/**
	 * The challenge ID used for testing.
	 */
	private const string CHALLENGE_ID = '01ARZ3NDEKTSV4RRFFQ69G5FAW';

	/**
	 * Mock for challenge secret delivery.
	 */
	private ChallengeSecretDeliveryInterface&MockObject $delivery;

	/**
	 * The strategy instance under test.
	 */
	private ChallengeCodeStrategy $strategy;

	/**
	 * Mock for challenge repository.
	 */
	private ChallengeRepositoryInterface&MockObject $challengeRepository;

	/**
	 * Mock for ULID generator.
	 */
	private MockObject&UlidGeneratorInterface $idGenerator;

	/**
	 * Mock for domain event publisher.
	 */
	private DomainEventPublisherInterface&MockObject $eventPublisher;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->delivery            = $this->createMock(ChallengeSecretDeliveryInterface::class);
		$this->challengeRepository = $this->createMock(ChallengeRepositoryInterface::class);
		$this->idGenerator         = $this->createMock(UlidGeneratorInterface::class);
		$this->eventPublisher      = $this->createMock(DomainEventPublisherInterface::class);

		$issueChallenge = new IssueChallenge($this->idGenerator, $this->challengeRepository, $this->eventPublisher);

		$this->strategy = new ChallengeCodeStrategy($issueChallenge, $this->delivery);
	}

	/**
	 * Test that getIdentifier returns the correct strategy identifier.
	 */
	#[Test]
	public function testGetIdentifier(): void
	{
		$this->assertSame('challenge_code', $this->strategy->getIdentifier());
	}

	/**
	 * Test that isSupported returns true for a matching identifier.
	 */
	#[Test]
	public function testIsSupportedWithMatchingIdentifier(): void
	{
		$request = $this->createStub(Credentials::class);
		$request
			->method('getStrategyIdentifier')
			->willReturn('challenge_code')
		;

		$this->assertTrue($this->strategy->isSupported($request));
	}

	/**
	 * Test that isSupported returns false for a non-matching identifier.
	 */
	#[Test]
	public function testIsSupportedWithNonMatchingIdentifier(): void
	{
		$request = $this->createStub(Credentials::class);
		$request
			->method('getStrategyIdentifier')
			->willReturn('challenge_token')
		;

		$this->assertFalse($this->strategy->isSupported($request));
	}

	/**
	 * Test that initiate issues a challenge code and delivers it.
	 */
	#[Test]
	public function testInitiateIssuesAndDeliversCode(): void
	{
		$userId          = UserId::fromString(self::USER_ID);
		$userCredentials = new UserCredentials(
			id: self::USER_ID,
			email: 'test@webifycms.com',
			displayName: 'Test User',
			passwordHash: 'hashed',
			status: 'active',
			isTwoFactorEnabled: true
		);
		$request = $this->createStub(Credentials::class);

		$this->challengeRepository
			->expects($this->once())
			->method('deleteStale')
			->with($userId)
		;
		$this->challengeRepository
			->expects($this->once())
			->method('persist')
			->with($this->isInstanceOf(Challenge::class))
		;

		$this->idGenerator
			->expects($this->once())
			->method('generate')
			->willReturn(self::CHALLENGE_ID)
		;

		$this->delivery
			->expects($this->once())
			->method('deliver')
			->with($userId, 'code', $this->matchesRegularExpression('/^\d{6}$/'))
		;

		$this->eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$this->strategy->initiate($request, $userCredentials);
	}
}
