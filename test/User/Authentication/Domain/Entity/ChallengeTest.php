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
use Webify\User\Authentication\Domain\Entity\Challenge;
use Webify\User\Authentication\Domain\Event\{ChallengeFailed, ChallengeVerified};
use Webify\User\Authentication\Domain\Exception\{ChallengeVerificationFailedException, InvalidChallengeSecretException};
use Webify\User\Authentication\Domain\ValueObject\{
	ChallengeCode,
	ChallengeId,
	ChallengeStatus,
	ChallengeToken,
	ChallengeType,
	UserId
};

/**
 * Tests for the Challenge entity.
 *
 * @internal
 */
#[CoversClass(Challenge::class)]
#[CoversMethod(Challenge::class, 'issue')]
#[CoversMethod(Challenge::class, 'getId')]
#[CoversMethod(Challenge::class, 'getUserId')]
#[CoversMethod(Challenge::class, 'getType')]
#[CoversMethod(Challenge::class, 'getSecret')]
#[CoversMethod(Challenge::class, 'getExpiresAt')]
#[CoversMethod(Challenge::class, 'getIssuedAt')]
#[CoversMethod(Challenge::class, 'getStatus')]
#[CoversMethod(Challenge::class, 'isVerified')]
#[CoversMethod(Challenge::class, 'isExpired')]
#[CoversMethod(Challenge::class, 'verify')]
final class ChallengeTest extends TestCase
{
	/**
	 * The challenge ID used for testing.
	 */
	private const string CHALLENGE_ID = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

	/**
	 * The user ID used for testing.
	 */
	private const string USER_ID = '01ARZ3NDEKTSV4RRFFQ69G5FAW';

	/**
	 * The challenge entity under test.
	 */
	private ChallengeId $challengeId;

	/**
	 * The user ID associated with the challenge.
	 */
	private UserId $userId;

	/**
	 * The expiration date and time of the challenge.
	 */
	private DateTime $expiresAt;

	/**
	 * {@inheritDoc}
	 */
	protected function setUp(): void
	{
		$this->challengeId = ChallengeId::fromString(self::CHALLENGE_ID);
		$this->userId      = UserId::fromString(self::USER_ID);
		$this->expiresAt   = DateTime::fromString('2099-01-01 00:00:00');
	}

	/**
	 * Tests issuing a code challenge.
	 */
	#[Test]
	public function testIssueCodeChallenge(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $this->expiresAt);

		$this->assertInstanceOf(ChallengeCode::class, $challenge->getSecret());
		$this->assertSame(ChallengeType::Code, $challenge->getType());
		$this->assertSame(ChallengeStatus::Pending, $challenge->getStatus());
		$this->assertFalse($challenge->isVerified());
	}

	/**
	 * Tests issuing a token challenge.
	 */
	#[Test]
	public function testIssueTokenChallenge(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Token, $this->expiresAt);

		$this->assertInstanceOf(ChallengeToken::class, $challenge->getSecret());
		$this->assertSame(ChallengeType::Token, $challenge->getType());
		$this->assertSame(ChallengeStatus::Pending, $challenge->getStatus());
		$this->assertFalse($challenge->isVerified());
	}

	/**
	 * Tests getters for challenge properties.
	 */
	#[Test]
	public function testChallengeGetters(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $this->expiresAt);

		$this->assertTrue($this->challengeId->equals($challenge->getId()));
		$this->assertTrue($this->userId->equals($challenge->getUserId()));
		$this->assertInstanceOf(DateTime::class, $challenge->getIssuedAt());
		$this->assertTrue($this->expiresAt->equals($challenge->getExpiresAt()));
	}

	/**
	 * Tests verifying a code challenge.
	 */
	#[Test]
	public function testVerifyCodeChallengeSuccessfully(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $this->expiresAt);

		/** @var ChallengeCode $secret */
		$secret = $challenge->getSecret();

		$challenge->verify($secret);
		$this->assertTrue($challenge->isVerified());
		$this->assertSame(ChallengeStatus::Completed, $challenge->getStatus());

		$events = $challenge->getDomainEvents();

		$this->assertCount(1, $events);
		$this->assertInstanceOf(ChallengeVerified::class, $events[0]);
	}

	/**
	 * Tests verifying a token challenge.
	 */
	#[Test]
	public function testVerifyTokenChallengeSuccessfully(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Token, $this->expiresAt);

		/** @var ChallengeToken $secret */
		$secret = $challenge->getSecret();

		$challenge->verify($secret);

		$this->assertTrue($challenge->isVerified());
		$this->assertSame(ChallengeStatus::Completed, $challenge->getStatus());

		$events = $challenge->getDomainEvents();

		$this->assertCount(1, $events);
		$this->assertInstanceOf(ChallengeVerified::class, $events[0]);
	}

	/**
	 * Tests verifying an expired challenge with an expected exception.
	 */
	#[Test]
	public function testVerifyExpiredChallengeThrowsException(): void
	{
		$expiredAt = DateTime::fromString('2020-01-01 00:00:00');
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $expiredAt);

		/** @var ChallengeCode $secret */
		$secret = $challenge->getSecret();

		$this->expectException(ChallengeVerificationFailedException::class);
		$challenge->verify($secret);
	}

	/**
	 * Tests verifying a challenge that has already been verified with an expected exception.
	 */
	#[Test]
	public function testVerifyAlreadyVerifiedChallengeThrowsException(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $this->expiresAt);

		/** @var ChallengeCode $secret */
		$secret = $challenge->getSecret();
		$challenge->verify($secret);
		$challenge->releaseDomainEvents();

		$this->expectException(ChallengeVerificationFailedException::class);
		$challenge->verify($secret);
	}

	/**
	 * Tests verifying a challenge with a wrong secret with an expected exception.
	 */
	#[Test]
	public function testVerifyWithWrongSecretThrowsException(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $this->expiresAt);

		$this->expectException(InvalidChallengeSecretException::class);
		$challenge->verify(ChallengeCode::fromString('999999'));
	}

	/**
	 * Tests verifying a challenge with a wrong secret expecting a `ChallengeFailed` event.
	 */
	#[Test]
	public function testVerifyRecordsChallengeFailedEventOnWrongSecret(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $this->expiresAt);

		try {
			$challenge->verify(ChallengeCode::fromString('999999'));
		} catch (InvalidChallengeSecretException) {
			$events = $challenge->getDomainEvents();
			$this->assertCount(1, $events);
			$this->assertInstanceOf(ChallengeFailed::class, $events[0]);
		}
	}

	/**
	 * Tests verifying a challenge with a secret after max attempts and expecting exceptions.
	 */
	#[Test]
	public function testMaxAttemptsLockout(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $this->expiresAt);

		// Use up all 6 attempts
		for ($i = 0; 6 > $i; ++$i) {
			try {
				$challenge->verify(ChallengeCode::fromString('999999'));
			} catch (InvalidChallengeSecretException) {
				$challenge->releaseDomainEvents();
			}
		}

		$this->expectException(ChallengeVerificationFailedException::class);
		$challenge->verify(ChallengeCode::fromString('999999'));
	}

	/**
	 * Tests the expired challenge with the `isExpired` method.
	 */
	#[Test]
	public function testIsExpiredReturnsTrueForExpiredChallenge(): void
	{
		$expiredAt = DateTime::fromString('2020-01-01 00:00:00');
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $expiredAt);

		$this->assertTrue($challenge->isExpired());
	}

	/**
	 * Tests the valid challenge with the `isExpired` method.
	 */
	#[Test]
	public function testIsExpiredReturnsFalseForValidChallenge(): void
	{
		$challenge = Challenge::issue($this->challengeId, $this->userId, ChallengeType::Code, $this->expiresAt);

		$this->assertFalse($challenge->isExpired());
	}
}
