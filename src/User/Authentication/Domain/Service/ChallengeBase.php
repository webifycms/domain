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

use DateTimeImmutable;
use DateTimeZone;
use Webify\Base\Domain\Contract\Authentication\{
	AuthenticatedUser,
	Request,
	StrategyInterface,
	UserCredentials,
	UserCredentialsLookupInterface
};
use Webify\Base\Domain\Contract\Authentication\Service\{ChallengeBasedInterface, StrategyRegisterInterface};
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Exception\{
	AuthenticationFailedException,
	ChallengeNotFoundException,
	InvalidChallengeSecretException
};
use Webify\User\Authentication\Domain\Guard\UserMustBeActive;
use Webify\User\Authentication\Domain\ValueObject\{
	AuthenticatedUser as AuthenticatedUserValueObject,
	ChallengeCode,
	ChallengeSecretInterface,
	ChallengeToken,
	ChallengeType,
	UserId
};

/**
 * Implementation of the challenge-based authentication service.
 */
final readonly class ChallengeBase implements ChallengeBasedInterface
{
	/**
	 * The number of minutes for which the session will be valid.
	 */
	private const int SESSION_TTL_MINUTES = 60;

	/**
	 * The constructor.
	 */
	public function __construct(
		private UserCredentialsLookupInterface $userLookup,
		private StrategyRegisterInterface $strategyRegister,
		private UserMustBeActive $userMustBeActive,
		private UserStatusTranslator $userStatusTranslator,
		private VerifyChallenge $verifyChallenge,
		private OpenSession $openSession
	) {}

	/**
	 * {@inheritDoc}
	 *
	 * @throws AuthenticationFailedException if the authentication initiation fails
	 */
	public function initiate(Request $credentials): void
	{
		$user = $this->userLookup($credentials);

		$this->userMustBeActive->guard($this->userStatusTranslator->translate($user->status));

		$strategy = $this->strategyLookup($credentials);

		$strategy->initiate($credentials, $user);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws AuthenticationFailedException if the authentication fails
	 */
	public function complete(Request $credentials): AuthenticatedUser
	{
		if (!$credentials->has('secret')) {
			throw AuthenticationFailedException::forMissingSecret();
		}

		$user = $this->userLookup($credentials);

		$this->userMustBeActive->guard($this->userStatusTranslator->translate($user->status));

		$userId        = UserId::fromString($user->id);
		$secret        = $this->identifySecretType($credentials);

		try {
			$this->verifyChallenge->verify($userId, $secret);
		} catch (ChallengeNotFoundException|InvalidChallengeSecretException) { // @phpstan-ignore catch.neverThrown
			throw AuthenticationFailedException::forInvalid();
		}

		$session = $this->openSession(UserId::fromString($user->id));

		return new AuthenticatedUser(
			$userId->toNative(),
			$user->email,
			$user->displayName,
			DateTime::now()->toNative(),
			$session->getAccessToken()->toNative(),
			$session->getRefreshToken()->toNative()
		);
	}

	/**
	 * Looks up the authentication strategy.
	 */
	private function strategyLookup(Request $credentials): StrategyInterface
	{
		$strategy = $this->strategyRegister->get($credentials->getStrategyIdentifier());

		if (!$strategy->isSupported($credentials)) {
			throw AuthenticationFailedException::strategyNotSupported($credentials->getStrategyIdentifier());
		}

		return $strategy;
	}

	/**
	 * Identifies the type of secret.
	 */
	private function identifySecretType(Request $credentials): ChallengeSecretInterface
	{
		$strategy = $this->strategyLookup($credentials);
		$type     = ChallengeType::tryFrom(str_replace('challenge_', '', $strategy->getIdentifier()));

		if (null === $type) {
			throw AuthenticationFailedException::forInvalid();
		}

		if ($type->isCode()) {
			return ChallengeCode::fromString($credentials->get('secret'));
		}

		return ChallengeToken::fromString($credentials->get('secret'));
	}

	/**
	 * Looks up the user credentials.
	 */
	private function userLookup(Request $credentials): UserCredentials
	{
		if (!$credentials->has('email')) {
			throw AuthenticationFailedException::forMissingEmail();
		}

		$email = $credentials->get('email');
		$user  = $this->userLookup->findByEmail($email);

		if (null === $user) {
			throw AuthenticationFailedException::userNotFound($email);
		}

		return $user;
	}

	/**
	 * Opens a session for the user.
	 */
	private function openSession(UserId $userId): Session
	{
		return $this->openSession->open(
			AuthenticatedUserValueObject::fromSuccessfulAuthentication($userId),
			new DateTimeImmutable(
				sprintf('+%d minutes', self::SESSION_TTL_MINUTES),
				new DateTimeZone('UTC')
			)
		);
	}
}
