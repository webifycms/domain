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
	AuthenticatedUser,
	Request,
	UserCredentials,
	UserCredentialsLookupInterface
};
use Webify\Base\Domain\Contract\Identity\Service\PasswordHasherInterface;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\User\Authentication\Domain\Entity\Challenge;
use Webify\User\Authentication\Domain\Exception\AuthenticationFailedException;
use Webify\User\Authentication\Domain\Guard\UserMustBeActive;
use Webify\User\Authentication\Domain\Repository\{ChallengeRepositoryInterface, SessionRepositoryInterface};
use Webify\User\Authentication\Domain\Service\{
	ChallengeSecretDeliveryInterface,
	IssueChallenge,
	OpenSession,
	PasswordBase,
	UserStatusTranslator
};
use Webify\User\Authentication\Domain\ValueObject\UserId;

use function in_array;

/**
 * Tests for the PasswordBase authentication service.
 *
 * @internal
 */
#[AllowMockObjectsWithoutExpectations]
#[CoversClass(PasswordBase::class)]
#[CoversMethod(PasswordBase::class, 'authenticate')]
final class PasswordBaseTest extends TestCase
{
	/**
	 * The user ID used for testing.
	 */
	private const string USER_ID = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

	/**
	 * Mock for user credentials lookup.
	 */
	private MockObject&UserCredentialsLookupInterface $credentialLookup;

	/**
	 * Mock for password hasher.
	 */
	private MockObject&PasswordHasherInterface $passwordHasher;

	/**
	 * Mock for challenge secret delivery.
	 */
	private ChallengeSecretDeliveryInterface&MockObject $delivery;

	/**
	 * The PasswordBase service instance under test.
	 */
	private PasswordBase $service;

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
		$this->credentialLookup    = $this->createMock(UserCredentialsLookupInterface::class);
		$this->passwordHasher      = $this->createMock(PasswordHasherInterface::class);
		$this->delivery            = $this->createMock(ChallengeSecretDeliveryInterface::class);
		$this->challengeRepository = $this->createMock(ChallengeRepositoryInterface::class);
		$this->sessionRepository   = $this->createMock(SessionRepositoryInterface::class);
		$this->idGenerator         = $this->createMock(UlidGeneratorInterface::class);
		$this->eventPublisher      = $this->createMock(DomainEventPublisherInterface::class);
		$issueChallenge            = new IssueChallenge(
			$this->idGenerator,
			$this->challengeRepository,
			$this->eventPublisher
		);
		$openSession               = new OpenSession(
			$this->sessionRepository,
			$this->idGenerator,
			$this->eventPublisher
		);
		$this->service             = new PasswordBase(
			$this->credentialLookup,
			$this->passwordHasher,
			new UserMustBeActive(),
			new UserStatusTranslator(),
			$issueChallenge,
			$openSession,
			$this->delivery
		);
	}

	/**
	 * Test authenticating a user successfully without 2FA.
	 */
	#[Test]
	public function testAuthenticateSuccessfullyWithout2FA(): void
	{
		$userCredentials = $this->createUserCredentials();
		$request         = $this->createRequest();

		$this->credentialLookup
			->expects($this->once())
			->method('findByEmail')
			->with('test@webifycms.com')
			->willReturn($userCredentials)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('verify')
			->with('correct_password', 'hashed_password')
			->willReturn(true)
		;
		$this->idGenerator
			->expects($this->once())
			->method('generate')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FAW')
		;
		$this->sessionRepository
			->expects($this->once())
			->method('persist')
		;
		$this->eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$result = $this->service->authenticate($request);

		$this->assertInstanceOf(AuthenticatedUser::class, $result);
		$this->assertSame(self::USER_ID, $result->id);
	}

	/**
	 * Test that authentication with 2FA enabled returns null and issues a challenge code.
	 */
	#[Test]
	public function testAuthenticateWith2FAReturnsNullAndIssuesCode(): void
	{
		$userCredentials = $this->createUserCredentials('active', true);
		$request         = $this->createRequest();

		$this->credentialLookup
			->expects($this->once())
			->method('findByEmail')
			->willReturn($userCredentials)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('verify')
			->with('correct_password', 'hashed_password')
			->willReturn(true)
		;
		$this->challengeRepository
			->expects($this->once())
			->method('deleteStale')
			->with($this->callback(fn (UserId $uid): bool => $uid->toNative() === self::USER_ID))
		;
		$this->challengeRepository
			->expects($this->once())
			->method('persist')
			->with($this->isInstanceOf(Challenge::class))
		;
		$this->idGenerator
			->expects($this->once())
			->method('generate')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FAW')
		;
		$this->delivery
			->expects($this->once())
			->method('deliver')
		;
		$this->eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$result = $this->service->authenticate($request);

		$this->assertNull($result);
	}

	/**
	 * Test that authenticate throws an exception when the email is missing.
	 */
	#[Test]
	public function testAuthenticateThrowsExceptionWhenEmailMissing(): void
	{
		$request = $this->createMock(Request::class);

		$request
			->method('has')
			->willReturnCallback(fn (string $key): bool => 'password' === $key)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->expectExceptionMessage('Email is required for authentication.');
		$this->service->authenticate($request);
	}

	/**
	 * Test that authenticate throws an exception when the password is missing.
	 */
	#[Test]
	public function testAuthenticateThrowsExceptionWhenPasswordMissing(): void
	{
		$request = $this->createMock(Request::class);

		$request
			->method('has')
			->willReturnCallback(fn (string $key): bool => 'email' === $key)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->expectExceptionMessage('Password is required for authentication.');
		$this->service->authenticate($request);
	}

	/**
	 * Test that authenticate throws an exception when the user is not found.
	 */
	#[Test]
	public function testAuthenticateThrowsExceptionWhenUserNotFound(): void
	{
		$request = $this->createRequest();

		$this->credentialLookup
			->expects($this->once())
			->method('findByEmail')
			->with('test@webifycms.com')
			->willReturn(null)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->expectExceptionMessage('User with email "test@webifycms.com" not found.');
		$this->service->authenticate($request);
	}

	/**
	 * Test that authenticate throws an exception when the user is not active.
	 */
	#[Test]
	public function testAuthenticateThrowsExceptionWhenUserNotActive(): void
	{
		$userCredentials = $this->createUserCredentials('unverified');
		$request         = $this->createRequest();

		$this->credentialLookup
			->expects($this->once())
			->method('findByEmail')
			->willReturn($userCredentials)
		;
		$this->passwordHasher
			->expects($this->never())
			->method('verify')
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->service->authenticate($request);
	}

	/**
	 * Test that authenticate throws an exception when the password is invalid.
	 */
	#[Test]
	public function testAuthenticateThrowsExceptionWhenPasswordInvalid(): void
	{
		$userCredentials = $this->createUserCredentials();
		$request         = $this->createRequest('test@webifycms.com', 'wrong_password');

		$this->credentialLookup
			->expects($this->once())
			->method('findByEmail')
			->willReturn($userCredentials)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('verify')
			->with('wrong_password', 'hashed_password')
			->willReturn(false)
		;
		$this->expectException(AuthenticationFailedException::class);
		$this->expectExceptionMessage('Invalid user credentials.');
		$this->service->authenticate($request);
	}

	/**
	 * Creates user credentials for testing.
	 */
	private function createUserCredentials(string $status = 'active', bool $isTwoFactorEnabled = false): UserCredentials
	{
		return new UserCredentials(
			id: self::USER_ID,
			email: 'test@webifycms.com',
			displayName: 'Test User',
			passwordHash: 'hashed_password',
			status: $status,
			isTwoFactorEnabled: $isTwoFactorEnabled
		);
	}

	/**
	 * Creates a request stub with the given email and password.
	 */
	private function createRequest(
		?string $email = 'test@webifycms.com',
		?string $password = 'correct_password'
	): Request {
		$request = $this->createStub(Request::class);

		$request
			->method('has')
			->willReturnCallback(fn (string $key): bool => in_array($key, ['email', 'password'], true))
		;
		$request
			->method('get')
			->willReturnCallback(fn (string $key) => match ($key) {
				'email'    => $email,
				'password' => $password,
				default    => null
			})
		;

		return $request;
	}
}
