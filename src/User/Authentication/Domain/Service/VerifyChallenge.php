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

use Webify\Base\Domain\Event\DomainEventPublisherInterface;
use Webify\User\Authentication\Domain\Entity\Challenge;
use Webify\User\Authentication\Domain\Exception\InvalidChallengeSecretException;
use Webify\User\Authentication\Domain\Repository\ChallengeRepositoryInterface;
use Webify\User\Authentication\Domain\ValueObject\{
	ChallengeSecretInterface,
	UserId
};

/**
 * VerifyChallenge domain service verifies the challenge secret and updates the challenge state accordingly.
 *
 * It is the uniform second step for all challenge-based flows and responsible for handling
 * the challenge verification process, persisting the increment attempt counter.
 */
final readonly class VerifyChallenge
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private ChallengeRepositoryInterface $repository,
		private DomainEventPublisherInterface $eventPublisher
	) {}

	/**
	 * Verifies the challenge secret and updates the challenge state accordingly.
	 *
	 * @throws InvalidChallengeSecretException if the secret is invalid
	 */
	public function verify(UserId $userId, ChallengeSecretInterface $secret): void
	{
		$challenge = $this->repository->getByUser($userId);

		try {
			$challenge->verify($secret);
		} catch (InvalidChallengeSecretException $exception) {
			// Persist the incremented attempt counter
			$this->flush($challenge);

			throw $exception;
		}

		// If the secret is valid, persist the consumed state
		$this->flush($challenge);
	}

	/**
	 * Persist the challenge entity and publish any associated events.
	 */
	private function flush(Challenge $challenge): void
	{
		$this->repository->persist($challenge);
		$this->eventPublisher->publish(...$challenge->getDomainEvents());
	}
}
