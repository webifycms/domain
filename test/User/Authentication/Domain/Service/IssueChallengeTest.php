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
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\User\Authentication\Domain\Entity\Challenge;
use Webify\User\Authentication\Domain\Repository\ChallengeRepositoryInterface;
use Webify\User\Authentication\Domain\Service\IssueChallenge;
use Webify\User\Authentication\Domain\ValueObject\{ChallengeType, UserId};

/**
 * Tests for the IssueChallenge domain service.
 *
 * @internal
 */
#[CoversClass(IssueChallenge::class)]
#[CoversMethod(IssueChallenge::class, 'issue')]
final class IssueChallengeTest extends TestCase
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
	 * Test issuing a code challenge for a user.
	 */
	#[Test]
	public function testIssueCodeChallenge(): void
	{
		$userId = UserId::fromString(self::USER_ID);

		$repository = $this->createMock(ChallengeRepositoryInterface::class);
		$repository
			->expects($this->once())
			->method('deleteStale')
			->with($userId)
		;
		$repository
			->expects($this->once())
			->method('persist')
			->with($this->isInstanceOf(Challenge::class))
		;

		$idGenerator = $this->createMock(UlidGeneratorInterface::class);

		$idGenerator
			->expects($this->once())
			->method('generate')
			->willReturn(self::CHALLENGE_ID)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$service   = new IssueChallenge($idGenerator, $repository, $eventPublisher);
		$challenge = $service->issue($userId, ChallengeType::Code);

		$this->assertInstanceOf(Challenge::class, $challenge);
		$this->assertTrue($challenge->getUserId()->equals($userId));
		$this->assertSame(ChallengeType::Code, $challenge->getType());
	}

	/**
	 * Test issuing a token challenge for a user.
	 */
	#[Test]
	public function testIssueTokenChallenge(): void
	{
		$userId     = UserId::fromString(self::USER_ID);
		$repository = $this->createMock(ChallengeRepositoryInterface::class);

		$repository
			->expects($this->once())
			->method('deleteStale')
			->with($userId)
		;
		$repository
			->expects($this->once())
			->method('persist')
		;

		$idGenerator = $this->createMock(UlidGeneratorInterface::class);

		$idGenerator
			->expects($this->once())
			->method('generate')
			->willReturn(self::CHALLENGE_ID)
		;

		$eventPublisher = $this->createMock(DomainEventPublisherInterface::class);

		$eventPublisher
			->expects($this->once())
			->method('publish')
		;

		$service   = new IssueChallenge($idGenerator, $repository, $eventPublisher);
		$challenge = $service->issue($userId, ChallengeType::Token);

		$this->assertInstanceOf(Challenge::class, $challenge);
		$this->assertTrue($challenge->getUserId()->equals($userId));
		$this->assertSame(ChallengeType::Token, $challenge->getType());
	}
}
