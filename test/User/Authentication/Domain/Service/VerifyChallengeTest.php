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
use Webify\User\Authentication\Domain\Entity\Challenge;
use Webify\User\Authentication\Domain\Exception\{ChallengeNotFoundException, InvalidChallengeSecretException};
use Webify\User\Authentication\Domain\Repository\ChallengeRepositoryInterface;
use Webify\User\Authentication\Domain\Service\VerifyChallenge;
use Webify\User\Authentication\Domain\ValueObject\{
	ChallengeCode,
	ChallengeId,
	ChallengeToken,
	ChallengeType,
	UserId
};

/**
 * Tests for the VerifyChallenge domain service.
 *
 * @internal
 */
#[CoversClass(VerifyChallenge::class)]
#[CoversMethod(VerifyChallenge::class, 'verify')]
final class VerifyChallengeTest extends TestCase
{
	/**
	 * User ID for testing.
	 */
	private const string USER_ID = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

	/**
	 * Challenge ID for testing.
	 */
	private const string CHALLENGE_ID = '01ARZ3NDEKTSV4RRFFQ69G5FAW';

	/**
	 * Test verifying a code challenge successfully.
	 */
	#[Test]
	public function testVerifyCodeChallengeSuccessfully(): void
	{
		$userId    = UserId::fromString(self::USER_ID);
		$challenge = Challenge::issue(
			ChallengeId::fromString(self::CHALLENGE_ID),
			$userId,
			ChallengeType::Code,
			DateTime::fromString('2099-01-01 00:00:00')
		);

		/** @var ChallengeCode $secret */
		$secret = $challenge->getSecret();

		$repository = $this->createMock(ChallengeRepositoryInterface::class);

		$repository
			->expects($this->once())
			->method('getByUser')
			->with($userId)
			->willReturn($challenge)
		;
		$repository
			->expects($this->once())
			->method('persist')
			->with($challenge)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$service = new VerifyChallenge($repository, $eventPublisher);

		$service->verify($userId, $secret);
		$this->assertTrue($challenge->isVerified());
	}

	/**
	 * Test verifying a token challenge successfully.
	 */
	#[Test]
	public function testVerifyTokenChallengeSuccessfully(): void
	{
		$userId    = UserId::fromString(self::USER_ID);
		$challenge = Challenge::issue(
			ChallengeId::fromString(self::CHALLENGE_ID),
			$userId,
			ChallengeType::Token,
			DateTime::fromString('2099-01-01 00:00:00')
		);

		/** @var ChallengeToken $secret */
		$secret = $challenge->getSecret();

		$repository = $this->createMock(ChallengeRepositoryInterface::class);

		$repository
			->expects($this->once())
			->method('getByUser')
			->with($userId)
			->willReturn($challenge)
		;
		$repository
			->expects($this->once())
			->method('persist')
			->with($challenge)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$service = new VerifyChallenge($repository, $eventPublisher);

		$service->verify($userId, $secret);
		$this->assertTrue($challenge->isVerified());
	}

	/**
	 * Test verifying a challenge with a wrong secret.
	 */
	#[Test]
	public function testVerifyWithWrongSecretPersistsAndThrows(): void
	{
		$userId    = UserId::fromString(self::USER_ID);
		$challenge = Challenge::issue(
			ChallengeId::fromString(self::CHALLENGE_ID),
			$userId,
			ChallengeType::Code,
			DateTime::fromString('2099-01-01 00:00:00')
		);
		$repository = $this->createMock(ChallengeRepositoryInterface::class);

		$repository
			->expects($this->once())
			->method('getByUser')
			->with($userId)
			->willReturn($challenge)
		;
		$repository
			->expects($this->once())
			->method('persist')
			->with($challenge)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher
			->expects($this->once())
			->method('publish')
		;
		$this->expectException(InvalidChallengeSecretException::class);

		$service = new VerifyChallenge($repository, $eventPublisher);

		$service->verify($userId, ChallengeCode::fromString('999999'));
	}

	/**
	 * Test verifying a challenge that does not exist and expecting an exception.
	 */
	#[Test]
	public function testVerifyThrowsExceptionWhenChallengeNotFound(): void
	{
		$userId     = UserId::fromString(self::USER_ID);
		$repository = $this->createMock(ChallengeRepositoryInterface::class);

		$repository
			->expects($this->once())
			->method('getByUser')
			->with($userId)
			->willThrowException(ChallengeNotFoundException::forId(self::CHALLENGE_ID))
		;
		$repository
			->expects($this->never())
			->method('persist')
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher
			->expects($this->never())
			->method('publish')
		;

		$this->expectException(ChallengeNotFoundException::class);

		$service = new VerifyChallenge($repository, $eventPublisher);

		$service->verify($userId, ChallengeCode::fromString('123456'));
	}
}
