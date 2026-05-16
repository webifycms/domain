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

namespace Webify\Test\User\Identity\Domain\Service;

use PHPUnit\Framework\Attributes\{AllowMockObjectsWithoutExpectations, CoversClass, CoversMethod, Test};
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Contract\Identity\PasswordHasherInterface;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\User\Identity\Domain\Entity\User;
use Webify\User\Identity\Domain\Exception\EmailMustBeUniqueException;
use Webify\User\Identity\Domain\Guard\EmailMustBeUnique;
use Webify\User\Identity\Domain\Repository\UserRepositoryInterface;
use Webify\User\Identity\Domain\Service\RegisterUser;
use Webify\User\Identity\Domain\ValueObject\UserEmail;

/**
 * RegisterUserTest tests the RegisterUser domain service.
 *
 * @internal
 */
#[CoversClass(RegisterUser::class)]
#[CoversMethod(RegisterUser::class, 'register')]
final class RegisterUserTest extends TestCase
{
	/**
	 * Password hasher service instance.
	 */
	private MockObject&PasswordHasherInterface $passwordHasher;

	/**
	 * Repository instance.
	 */
	private MockObject&UserRepositoryInterface $repository;

	/**
	 * Ulid generator service instance.
	 */
	private MockObject&UlidGeneratorInterface $ulidGenerator;

	/**
	 * Domain event publisher service instance.
	 */
	private DomainEventPublisherInterface&MockObject $eventPublisher;

	/**
	 * The RegisterUser instance.
	 */
	private RegisterUser $registerUser;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->passwordHasher = $this->createMock(PasswordHasherInterface::class);
		$this->repository     = $this->createMock(UserRepositoryInterface::class);
		$this->ulidGenerator  = $this->createMock(UlidGeneratorInterface::class);
		$this->eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$this->registerUser = new RegisterUser(
			$this->passwordHasher,
			$this->repository,
			new EmailMustBeUnique($this->repository),
			$this->ulidGenerator,
			$this->eventPublisher
		);
	}

	/**
	 * Tests the successful registration of a new user.
	 */
	#[Test]
	public function testRegisterUserSuccessfully(): void
	{
		$email        = 'test@example.com';
		$password     = 'password123';
		$displayName  = 'Test User';
		$generatedId  = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$hashedPass   = password_hash($password, PASSWORD_DEFAULT);

		$this->repository
			->expects($this->once())
			->method('isExists')
			->with($this->callback(
				function (UserEmail $userEmail) use ($email): bool {
					return $userEmail->toNative() === $email;
				}
			))
			->willReturn(false)
		;
		$this->passwordHasher
			->expects($this->once())
			->method('hash')
			->with($password)
			->willReturn($hashedPass)
		;
		$this->ulidGenerator
			->expects($this->once())
			->method('generate')
			->willReturn($generatedId)
		;
		$this->repository
			->expects($this->once())
			->method('persist')
			->with($this->isInstanceOf(User::class))
		;
		$this->eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$this->registerUser->register($email, $password, $displayName);
	}

	/**
	 * Tests that registration throws an exception when the email already exists.
	 */
	#[Test]
	#[AllowMockObjectsWithoutExpectations]
	public function testRegisterThrowsExceptionWhenEmailAlreadyExists(): void
	{
		$email = 'existing@example.com';

		$this->repository
			->expects($this->once())
			->method('isExists')
			->with($this->callback(
				function (UserEmail $userEmail) use ($email): bool {
					return $userEmail->toNative() === $email;
				}
			))
			->willReturn(true)
		;
		$this->expectException(EmailMustBeUniqueException::class);
		$this->registerUser->register($email, 'password123', 'Test User');
	}
}
