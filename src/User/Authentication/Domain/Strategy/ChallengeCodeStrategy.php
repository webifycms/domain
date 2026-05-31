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

namespace Webify\User\Authentication\Domain\Strategy;

use Webify\Base\Domain\Contract\Authentication\{
	Request,
	StrategyInterface,
	UserCredentials
};
use Webify\User\Authentication\Domain\Service\{ChallengeSecretDeliveryInterface, IssueChallenge};
use Webify\User\Authentication\Domain\ValueObject\{ChallengeCode, ChallengeType, UserId};

/**
 * ChallengeCodeStrategy handles challenge code authentication flow that sent via email or any other channels.
 */
final readonly class ChallengeCodeStrategy implements StrategyInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private IssueChallenge $issueChallenge,
		private ChallengeSecretDeliveryInterface $delivery,
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function getIdentifier(): string
	{
		return 'challenge_code';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSupported(Request $credentials): bool
	{
		return $this->getIdentifier() === $credentials->getStrategyIdentifier();
	}

	/**
	 * {@inheritDoc}
	 */
	public function initiate(
		Request $requestCredentials,
		UserCredentials $userCredentials
	): void {
		$type      = ChallengeType::Code;
		$userId    = UserId::fromString($userCredentials->id);
		$challenge = $this->issueChallenge->issue($userId, $type);

		/** @var ChallengeCode $challengeCode */
		$challengeCode = $challenge->getSecret();

		// Deliver the challenge code to the user
		$this->delivery->deliver($userId, $type->value, $challengeCode->toNative());
	}
}
