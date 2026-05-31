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

use PHPUnit\Framework\Attributes\{AllowMockObjectsWithoutExpectations, CoversClass, CoversMethod, Test};
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Contract\Authentication\{
	AuthenticatedUser as AuthenticatedUserDTO,
	Credentials,
	StrategyInterface,
	UserCredentials,
	UserCredentialsLookupInterface
};
use Webify\Base\Domain\Contract\Authentication\Service\StrategyRegisterInterface;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Entity\Challenge;
use Webify\User\Authentication\Domain\Exception\{
	AuthenticationFailedException,
	ChallengeNotFoundException
};
use Webify\User\Authentication\Domain\Guard\UserMustBeActive;
use Webify\User\Authentication\Domain\Repository\{ChallengeRepositoryInterface, SessionRepositoryInterface};
use Webify\User\Authentication\Domain\Service\{ChallengeBase, OpenSession, UserStatusTranslator, VerifyChallenge};
use Webify\User\Authentication\Domain\ValueObject\{ChallengeCode, ChallengeId, ChallengeType, UserId};

use function in_array;

/**
 * Tests for the ChallengeBase authentication service.
 *
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
#[CoversClass(ChallengeBase::class)]
#[CoversMethod(ChallengeBase::class, 'initiate')]
#[CoversMethod(ChallengeBase::class, 'complete')]
final class ChallengeBaseTest extends TestCase
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
	 * Mock for user credentials lookup service.
	 */
	private MockObject&UserCredentialsLookupInterface $userLookup;

	/**
	 * Mock for the strategy register.
	 */
	private MockObject&StrategyRegisterInterface $strategyRegister;

	/**
	 * The ChallengeBase service instance under test.
	 */
	private ChallengeBase $service;

	/**
	 * Mock for challenge repository.
	 */
	private ChallengeRepositoryInterface&MockObject $challengeRepository;

	/**
	 * Mock for session repository.
	 */
	private MockObject&SessionRepositoryInterface $sessionRepository;

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
		$this->userLookup           = $this->createMock(UserCredentialsLookupInterface::class);
		$this->strategyRegister     = $this->createMock(StrategyRegisterInterface::class);
		$this->challengeRepository  = $this->createMock(ChallengeRepositoryInterface::class);
		$this->sessionRepository    = $this->createMock(SessionRepositoryInterface::class);
		$this->idGenerator          = $this->createMock(UlidGeneratorInterface::class);
		$this->eventPublisher       = $this->createMock(DomainEventPublisherInterface::class);
		$verifyChallenge            = new VerifyChallenge($this->challengeRepository, $this->eventPublisher);
		$openSession                = new OpenSession(
			$this->sessionRepository,
			$this->idGenerator,
			$this->eventPublisher
		);
		$this->service              = new ChallengeBase(
			$this->userLookup,
			$this->strategyRegister,
			new UserMustBeActive(),
			new UserStatusTranslator(),
			$verifyChallenge,
			$openSession
		);
	}

	/**
	 * Test initiating challenge-based authentication successfully.
	 */
	#[Test]
	public function testInitiateSuccessfully(): void
	{
		$userCredentials = $this->createUserCredentials();
		$request         = $this->createRequest('challenge_code');
		$strategy        = $this->createMock(StrategyInterface::class);

		$strategy
			->method('getIdentifier')
			->willReturn('challenge_code')
		;
		$strategy
			->method('isSupported')
			->willReturn(true)
		;
		$this->userLookup
			->expects($this->once())
			->method('findByEmail')
			->with('test@webifycms.com')
			->willReturn($userCredentials)
		;
		$this->strategyRegister
			->expects($this->once())
			->method('get')
			->with('challenge_code')
			->willReturn($strategy)
		;
		$strategy
			->expects($this->once())
			->method('initiate')
			->with($request, $userCredentials)
		;
		$this->service->initiate($request);
	}

	/**
	 * Test that initiate throws an exception when the user is not found.
	 */
	#[Test]
	public function testInitiateThrowsExceptionWhenUserNotFound(): void
	{
		$request = $this->createRequest('challenge_code');

		$this->userLookup
			->expects($this->once())
			->method('findByEmail')
			->with('test@webifycms.com')
			->willReturn(null)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->service->initiate($request);
	}

	/**
	 * Test that initiate throws an exception when the user is not active.
	 */
	#[Test]
	public function testInitiateThrowsExceptionWhenUserNotActive(): void
	{
		$userCredentials = $this->createUserCredentials('unverified');
		$request         = $this->createRequest('challenge_code');

		$this->userLookup
			->expects($this->once())
			->method('findByEmail')
			->willReturn($userCredentials)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->service->initiate($request);
	}

	/**
	 * Test that initiate throws an exception when the strategy is not supported.
	 */
	#[Test]
	public function testInitiateThrowsExceptionWhenStrategyNotSupported(): void
	{
		$userCredentials = $this->createUserCredentials();
		$request         = $this->createRequest('unsupported_strategy');
		$strategy        = $this->createMock(StrategyInterface::class);

		$strategy
			->method('getIdentifier')
			->willReturn('unsupported_strategy')
		;
		$strategy
			->method('isSupported')
			->willReturn(false)
		;
		$this->userLookup
			->expects($this->once())
			->method('findByEmail')
			->willReturn($userCredentials)
		;
		$this->strategyRegister
			->expects($this->once())
			->method('get')
			->with('unsupported_strategy')
			->willReturn($strategy)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->service->initiate($request);
	}

	/**
	 * Test completing a code challenge successfully.
	 */
	#[Test]
	public function testCompleteCodeChallengeSuccessfully(): void
	{
		$userCredentials = $this->createUserCredentials();
		$challenge       = Challenge::issue(
			ChallengeId::fromString(self::CHALLENGE_ID),
			UserId::fromString(self::USER_ID),
			ChallengeType::Code,
			DateTime::fromString('2099-01-01 00:00:00')
		);

		/** @var ChallengeCode $secret */
		$secret = $challenge->getSecret();

		$request = $this->createRequest('challenge_code', $secret->toNative());

		$this->userLookup
			->expects($this->once())
			->method('findByEmail')
			->willReturn($userCredentials)
		;

		$strategy = $this->createMockStrategy('challenge_code');

		$this->strategyRegister
			->expects($this->once())
			->method('get')
			->with('challenge_code')
			->willReturn($strategy)
		;
		$this->challengeRepository
			->expects($this->once())
			->method('getByUser')
			->willReturn($challenge)
		;
		$this->challengeRepository
			->expects($this->once())
			->method('persist')
			->with($challenge)
		;
		$this->idGenerator
			->expects($this->once())
			->method('generate')
			->willReturn(self::CHALLENGE_ID)
		;
		$this->sessionRepository
			->expects($this->once())
			->method('persist')
		;
		$this->eventPublisher
			->expects($this->exactly(2))
			->method('publish')
		;
		$result = $this->service->complete($request);

		$this->assertInstanceOf(AuthenticatedUserDTO::class, $result);
		$this->assertSame(self::USER_ID, $result->id);
		$this->assertSame('test@webifycms.com', $result->email);
	}

	/**
	 * Test that complete throws an exception when the secret is missing.
	 */
	#[Test]
	public function testCompleteThrowsExceptionWhenSecretMissing(): void
	{
		$request = $this->createMock(Credentials::class);

		$request
			->method('has')
			->willReturnCallback(fn (string $key): bool => 'email' === $key)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->expectExceptionMessage('Secret is required for passwordless authentication.');
		$this->service->complete($request);
	}

	/**
	 * Test that complete throws an exception when the email is missing.
	 */
	#[Test]
	public function testCompleteThrowsExceptionWhenEmailMissing(): void
	{
		$request = $this->createMock(Credentials::class);

		$request
			->method('has')
			->willReturnCallback(fn (string $key): bool => 'secret' === $key)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->expectExceptionMessage('Email is required for authentication.');
		$this->service->complete($request);
	}

	/**
	 * Test that complete throws an exception when the user is not found.
	 */
	#[Test]
	public function testCompleteThrowsExceptionWhenUserNotFound(): void
	{
		$request = $this->createRequest('challenge_code');

		$this->userLookup
			->expects($this->once())
			->method('findByEmail')
			->willReturn(null)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->service->complete($request);
	}

	/**
	 * Test that complete throws an exception when the challenge is not found.
	 */
	#[Test]
	public function testCompleteThrowsExceptionWhenChallengeNotFound(): void
	{
		$userCredentials = $this->createUserCredentials();
		$request         = $this->createRequest('challenge_code');

		$this->userLookup
			->expects($this->once())
			->method('findByEmail')
			->willReturn($userCredentials)
		;

		$strategy = $this->createMockStrategy('challenge_code');

		$this->strategyRegister
			->expects($this->once())
			->method('get')
			->with('challenge_code')
			->willReturn($strategy)
		;
		$this->challengeRepository
			->expects($this->once())
			->method('getByUser')
			->willThrowException(
				ChallengeNotFoundException::forId(self::CHALLENGE_ID)
			)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->service->complete($request);
	}

	/**
	 * Test that complete throws an exception when the secret is invalid.
	 */
	#[Test]
	public function testCompleteThrowsExceptionWhenSecretInvalid(): void
	{
		$userCredentials = $this->createUserCredentials();
		$request         = $this->createRequest('challenge_code', '999999');
		$challenge       = Challenge::issue(
			ChallengeId::fromString(self::CHALLENGE_ID),
			UserId::fromString(self::USER_ID),
			ChallengeType::Code,
			DateTime::fromString('2099-01-01 00:00:00')
		);

		$this->userLookup
			->expects($this->once())
			->method('findByEmail')
			->willReturn($userCredentials)
		;

		$strategy = $this->createMockStrategy('challenge_code');

		$this->strategyRegister
			->expects($this->once())
			->method('get')
			->with('challenge_code')
			->willReturn($strategy)
		;
		$this->challengeRepository
			->expects($this->once())
			->method('getByUser')
			->willReturn($challenge)
		;
		$this->challengeRepository
			->expects($this->once())
			->method('persist')
		;
		$this->eventPublisher
			->expects($this->once())
			->method('publish')
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->service->complete($request);
	}

	/**
	 * Creates user credentials for testing.
	 */
	private function createUserCredentials(string $status = 'active'): UserCredentials
	{
		return new UserCredentials(
			id: self::USER_ID,
			email: 'test@webifycms.com',
			displayName: 'Test User',
			passwordHash: 'hashed_password',
			status: $status,
			isTwoFactorEnabled: true
		);
	}

	/**
	 * Creates a mock strategy stub with the given identifier.
	 */
	private function createMockStrategy(string $identifier): StrategyInterface
	{
		$strategy = $this->createStub(StrategyInterface::class);

		$strategy
			->method('getIdentifier')
			->willReturn($identifier)
		;
		$strategy
			->method('isSupported')
			->willReturn(true)
		;

		return $strategy;
	}

	/**
	 * Creates a request stub with the given strategy, secret, and email.
	 */
	private function createRequest(
		string $strategy,
		?string $secret = '123456',
		?string $email = 'test@webifycms.com'
	): Credentials {
		$request = $this->createStub(Credentials::class);

		$request
			->method('getStrategyIdentifier')
			->willReturn($strategy)
		;
		$request
			->method('has')
			->willReturnCallback(fn (string $key): bool => in_array($key, ['email', 'secret'], true))
		;
		$request
			->method('get')
			->willReturnCallback(fn (string $key) => match ($key) {
				'email'  => $email,
				'secret' => $secret,
				default  => null
			})
		;

		return $request;
	}
}
