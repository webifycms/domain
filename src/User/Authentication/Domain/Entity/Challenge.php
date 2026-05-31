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

namespace Webify\User\Authentication\Domain\Entity;

use Webify\Base\Domain\Entity\AggregateRoot;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Event\{ChallengeFailed, ChallengeVerified};
use Webify\User\Authentication\Domain\Exception\{ChallengeVerificationFailedException, InvalidChallengeSecretException};
use Webify\User\Authentication\Domain\ValueObject\{
	ChallengeCode,
	ChallengeId,
	ChallengeSecretInterface,
	ChallengeStatus,
	ChallengeToken,
	ChallengeType,
	UserId
};

/**
 * Challenge aggregate root.
 *
 * The aggregate root to handle the 2FA authentication challenge It should support challenge types.
 * The aggregate protects the invariants that make these flows secure:
 * - A verified challenge cannot be verified again (replay protection).
 * - An expired challenge cannot be verified (time-bounding).
 * - Excessive failed attempts to lock the challenge (brute-force protection).
 */
final class Challenge extends AggregateRoot
{
	/**
	 * The maximum number of attempts allowed for a challenge.
	 */
	private const int MAX_ATTEMPTS = 6;

	/**
	 * Indicates whether the challenge has been verified.
	 */
	private bool $isVerified = false;

	/**
	 * The number of attempts made to verify the challenge.
	 */
	private int $attempts = 0;

	/**
	 * Private constructor enforces the use of the factory methods to initiate this entity.
	 */
	private function __construct(
		private readonly ChallengeId $id,
		private readonly UserId $userId,
		private readonly ChallengeType $type,
		private readonly ChallengeSecretInterface $secret,
		private readonly DateTime $expiresAt,
		private readonly DateTime $issuedAt,
		private ChallengeStatus $status
	) {}

	/**
	 * Returns the identifier of the challenge.
	 */
	public function getId(): ChallengeId
	{
		return $this->id;
	}

	/**
	 * Returns the identifier of the user associated with the challenge.
	 */
	public function getUserId(): UserId
	{
		return $this->userId;
	}

	/**
	 * Returns the type of the challenge.
	 */
	public function getType(): ChallengeType
	{
		return $this->type;
	}

	/**
	 * Returns the secret associated with the challenge.
	 */
	public function getSecret(): ChallengeSecretInterface
	{
		return $this->secret;
	}

	/**
	 * Returns the expiration date of the challenge.
	 */
	public function getExpiresAt(): DateTime
	{
		return $this->expiresAt;
	}

	/**
	 * Returns the date and time when the challenge was issued.
	 */
	public function getIssuedAt(): DateTime
	{
		return $this->issuedAt;
	}

	/**
	 * Returns the status of the challenge.
	 */
	public function getStatus(): ChallengeStatus
	{
		return $this->status;
	}

	/**
	 * Checks if the challenge has been verified.
	 */
	public function isVerified(): bool
	{
		return $this->isVerified;
	}

	/**
	 * Checks if the challenge has expired.
	 */
	public function isExpired(): bool
	{
		return $this->expiresAt->isBefore(DateTime::now());
	}

	/**
	 * Verifies the challenge using the provided secret.
	 *
	 * @throws ChallengeVerificationFailedException if the challenge is expired, already verified,
	 *                                              or exceeds the maximum number of attempts
	 * @throws InvalidChallengeSecretException      if the secret is invalid
	 */
	public function verify(ChallengeSecretInterface $secret): void
	{
		if ($this->isExpired()) {
			throw ChallengeVerificationFailedException::forExpired($this->type->value);
		}

		if ($this->isVerified()) {
			throw ChallengeVerificationFailedException::forAlreadyVerified($this->type->value);
		}

		if (self::MAX_ATTEMPTS <= $this->attempts) {
			throw ChallengeVerificationFailedException::forMaximumAttempts($this->type->value);
		}

		if (!$this->secret->verify($secret)) {
			++$this->attempts;

			$this->recordDomainEvent(
				new ChallengeFailed(
					$this->id->toNative(),
					$this->userId->toNative(),
					$this->type->value,
					$this->attempts,
					DateTime::now()->toNative()
				)
			);

			throw InvalidChallengeSecretException::create();
		}

		$this->isVerified = true;
		$this->status     = ChallengeStatus::Completed;

		$this->recordDomainEvent(
			new ChallengeVerified(
				$this->id->toNative(),
				$this->userId->toNative(),
				$this->type->value,
				$this->expiresAt->toNative(),
				$this->issuedAt->toNative(),
				$this->status->value,
				DateTime::now()->toNative()
			)
		);
	}

	/**
	 * Issues a new challenge for the specified user.
	 */
	public static function issue(
		ChallengeId $id,
		UserId $userId,
		ChallengeType $type,
		DateTime $expiresAt
	): self {
		$secret = $type->isCode() ? ChallengeCode::generate() : ChallengeToken::generate();

		return new self($id, $userId, $type, $secret, $expiresAt, DateTime::now(), ChallengeStatus::Pending);
	}
}
