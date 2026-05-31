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
	Credentials,
	StrategyInterface,
	UserCredentials
};
use Webify\User\Authentication\Domain\Service\{ChallengeSecretDeliveryInterface, IssueChallenge};
use Webify\User\Authentication\Domain\ValueObject\{ChallengeToken, ChallengeType, UserId};

/**
 * ChallengeTokenStrategy handles challenge token authentication flow (via a magic link) that sent by email.
 */
final readonly class ChallengeTokenStrategy implements StrategyInterface
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
		return 'challenge_token';
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSupported(Credentials $credentials): bool
	{
		return $this->getIdentifier() === $credentials->getStrategyIdentifier();
	}

	/**
	 * {@inheritDoc}
	 */
	public function initiate(
		Credentials $requestCredentials,
		UserCredentials $userCredentials
	): void {
		$type      = ChallengeType::Token;
		$userId    = UserId::fromString($userCredentials->id);
		$challenge = $this->issueChallenge->issue($userId, $type);

		/** @var ChallengeToken $challengeToken */
		$challengeToken = $challenge->getSecret();

		// Deliver the challenge token via a link to the user
		$this->delivery->deliver($userId, $type->value, $challengeToken->toNative());
	}
}
