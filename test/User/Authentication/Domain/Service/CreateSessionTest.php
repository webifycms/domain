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
use Webify\Base\Domain\Contract\Authentication\{UserCredential, UserCredentialLookupInterface};
use Webify\Base\Domain\Contract\Identity\PasswordHasherInterface;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Exception\{InvalidUserCredentialException, UserNotEligibleForAuthenticationException};
use Webify\User\Authentication\Domain\Guard\UserMustBeActive;
use Webify\User\Authentication\Domain\Repository\SessionRepositoryInterface;
use Webify\User\Authentication\Domain\Service\{CreateSession, UserStatusTranslator};
use Webify\User\Authentication\Domain\ValueObject\UserId;

/**
 * CreateSessionTest tests the CreateSession domain service.
 *
 * @internal
 */
#[CoversClass(CreateSession::class)]
#[CoversMethod(CreateSession::class, 'create')]
final class CreateSessionTest extends TestCase
{
	/**
	 * Credential lookup service instance.
	 */
	private MockObject&UserCredentialLookupInterface $credentialLookup;

	/**
	 * Password hasher service instance.
	 */
	private MockObject&PasswordHasherInterface $passwordHasher;

	/**
	 * Repository instance.
	 */
	private MockObject&SessionRepositoryInterface $repository;

	/**
	 * Ulid generator service instance.
	 */
	private MockObject&UlidGeneratorInterface $idGenerator;

	/**
	 * Domain event publisher service instance.
	 */
	private DomainEventPublisherInterface&MockObject $eventPublisher;

	/**
	 * The CreateSession instance.
	 */
	private CreateSession $createSession;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->credentialLookup = $this->createMock(UserCredentialLookupInterface::class);
		$this->passwordHasher   = $this->createMock(PasswordHasherInterface::class);
		$this->repository       = $this->createMock(SessionRepositoryInterface::class);
		$this->idGenerator      = $this->createMock(UlidGeneratorInterface::class);
		$this->eventPublisher   = $this->createMock(DomainEventPublisherInterface::class);

		$this->createSession = new CreateSession(
			$this->credentialLookup,
			new UserMustBeActive(),
			$this->passwordHasher,
			$this->repository,
			$this->idGenerator,
			new UserStatusTranslator(),
			$this->eventPublisher
		);
	}

	/**
	 * Tests the successful creation of a session for a user with valid credentials.
	 *
	 * This method verifies that the session creation process is executed correctly by:
	 * - Ensuring the user credentials are fetched using the provided email.
	 * - Validating the provided password against the stored password hash.
	 * - Generating a new unique session ID.
	 * - Persisting the session data in the repository.
	 * - Publishing a domain event for the created session.
	 *
	 * It asserts that:
	 * - The returned session is an instance of the Session class.
	 * - The session contains the correct user ID corresponding to the provided credentials.
	 */
	#[Test]
	public function testCreateSessionSuccessfully(): void
	{
		$email        = 'test@example.com';
		$password     = 'password123';
		$expiresAt    = new DateTimeImmutable('2099-01-01 00:00:00');
		$userId       = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$generatedId  = '01ARZ3NDEKTSV4RRFFQ69G5FAW';
		$credential   = new UserCredential(
			id: $userId,
			email: $email,
			passwordHash: $passwordHash,
			userStatus: 'active'
		);

		$this->credentialLookup
			->expects($this->once())
			->method('findByEmail')
			->with($email)
			->willReturn($credential)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('verify')
			->with($password, $passwordHash)
			->willReturn(true)
		;
		$this->idGenerator
			->expects($this->once())
			->method('generate')
			->willReturn($generatedId)
		;
		$this->repository
			->expects($this->once())
			->method('persist')
			->with($this->isInstanceOf(Session::class))
		;
		$this->eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$session = $this->createSession->create($email, $password, $expiresAt);

		$this->assertInstanceOf(Session::class, $session);
		$this->assertTrue($session->getUserId()->equals(UserId::fromString($userId)));
	}

	/**
	 * Tests that the create method throws an InvalidUserCredentialException when the specified email is not found.
	 */
	#[Test]
	#[AllowMockObjectsWithoutExpectations]
	public function testCreateThrowsExceptionWhenEmailNotFound(): void
	{
		$this->credentialLookup
			->expects($this->once())
			->method('findByEmail')
			->with('unknown@example.com')
			->willReturn(null)
		;
		$this->expectException(InvalidUserCredentialException::class);
		$this->createSession->create(
			'unknown@example.com',
			'any_password',
			new DateTimeImmutable('2099-01-01 00:00:00')
		);
	}

	/**
	 * Tests that an exception is thrown during session creation
	 * when the provided password is invalid.
	 *
	 * The method verifies that the `InvalidUserCredentialException`
	 * is raised if the injected password does not match the stored
	 * password hash for a given user.
	 */
	#[Test]
	#[AllowMockObjectsWithoutExpectations]
	public function testCreateThrowsExceptionWhenPasswordIsInvalid(): void
	{
		$email        = 'test@example.com';
		$passwordHash = password_hash('correct_password', PASSWORD_DEFAULT);
		$credential   = new UserCredential(
			id: '01ARZ3NDEKTSV4RRFFQ69G5FAV',
			email: $email,
			passwordHash: $passwordHash,
			userStatus: 'active'
		);

		$this->credentialLookup
			->expects($this->once())
			->method('findByEmail')
			->with($email)
			->willReturn($credential)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('verify')
			->with('wrong_password', $passwordHash)
			->willReturn(false)
		;
		$this->expectException(InvalidUserCredentialException::class);
		$this->createSession->create(
			$email,
			'wrong_password',
			new DateTimeImmutable('2099-01-01 00:00:00')
		);
	}

	/**
	 * Tests that an exception is thrown when attempting to create a session for a user
	 * with an inactive or unverified status.
	 */
	#[Test]
	#[AllowMockObjectsWithoutExpectations]
	public function testCreateThrowsExceptionWhenUserNotActive(): void
	{
		$email        = 'test@example.com';
		$password     = 'password123';
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$credential   = new UserCredential(
			id: '01ARZ3NDEKTSV4RRFFQ69G5FAV',
			email: $email,
			passwordHash: $passwordHash,
			userStatus: 'unverified'
		);

		$this->credentialLookup
			->expects($this->once())
			->method('findByEmail')
			->with($email)
			->willReturn($credential)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('verify')
			->with($password, $passwordHash)
			->willReturn(true)
		;

		$this->expectException(UserNotEligibleForAuthenticationException::class);
		$this->createSession->create($email, $password, new DateTimeImmutable('2099-01-01 00:00:00'));
	}
}
