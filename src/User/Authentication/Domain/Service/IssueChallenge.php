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

namespace Webify\User\Authentication\Domain\Service;

use DateInvalidTimeZoneException;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;
use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\Base\Domain\Exception\DateTimeException;
use Webify\Base\Domain\Service\UlidGeneratorInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Entity\Challenge;
use Webify\User\Authentication\Domain\Repository\ChallengeRepositoryInterface;
use Webify\User\Authentication\Domain\ValueObject\{ChallengeId, ChallengeType, UserId};

/**
 * IssueChallenge creates and persists a new challenge, triggers delivery,
 * and returns a PendingChallenge descriptor to the caller.
 *
 * It cleans up stale challenges for the user before issuing a new one,
 * ensuring the repository does not accumulate unbounded rows.
 *
 * TTL constants are intentionally placed here as a single source of truth.
 * If per-tenant or per-installation TTL configuration is needed in the future,
 * replace the constants with a ChallengeTtlPolicy value object injected
 * via the constructor.
 */
final readonly class IssueChallenge
{
	/**
	 * The number of minutes a challenge is live for.
	 */
	private const int CHALLENGE_TTL_MINUTES = 10;

	/**
	 * The constructor.
	 */
	public function __construct(
		private UlidGeneratorInterface $idGenerator,
		private ChallengeRepositoryInterface $repository,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Issues a new challenge for the given user and type.
	 *
	 * @param UserId        $userId the user ID to issue the challenge for
	 * @param ChallengeType $type   the type of the challenge @see ChallengeType
	 *
	 * @return Challenge the issued challenge
	 */
	public function issue(UserId $userId, ChallengeType $type): Challenge
	{
		$this->repository->deleteStale($userId);

		$challenge = Challenge::issue(
			ChallengeId::fromString($this->idGenerator->generate()),
			$userId,
			$type,
			$this->expiryFromNow()
		);

		$this->repository->persist($challenge);
		$this->eventPublisher->publish(...$challenge->getDomainEvents());

		return $challenge;
	}

	/**
	 * Creates and return a DateTime object representing the current time plus the TTL.
	 *
	 * @throws DateTimeException if the DateTime object cannot be created
	 */
	private function expiryFromNow(): DateTime
	{
		$datetime = sprintf('+%d minutes', self::CHALLENGE_TTL_MINUTES);

		try {
			return DateTime::fromNative(new DateTimeImmutable($datetime, new DateTimeZone('UTC')));
		} catch (DateInvalidTimeZoneException $exception) {
			throw DateTimeException::forInvalidTimezone(value: 'UTC', previous: $exception);
		} catch (DateMalformedStringException $exception) {
			throw DateTimeException::forInvalidDatetime(value: $datetime, previous: $exception);
		}
	}
}
