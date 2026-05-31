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
	Service\PasswordBaseInterface,
	UserCredentials,
	UserCredentialsLookupInterface
};
use Webify\Base\Domain\Contract\Identity\Service\PasswordHasherInterface;
use Webify\Base\Domain\ValueObject\DateTime;
use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Exception\AuthenticationFailedException;
use Webify\User\Authentication\Domain\Guard\UserMustBeActive;
use Webify\User\Authentication\Domain\ValueObject\{
	AuthenticatedUser as AuthenticatedUserValueObject,
	ChallengeCode,
	ChallengeType,
	UserId
};

/**
 * The implementation of the password base authentication service.
 */
final readonly class PasswordBase implements PasswordBaseInterface
{
	/**
	 * The number of minutes for which the session will be valid.
	 */
	private const int SESSION_TTL_MINUTES = 60;

	/**
	 * The constructor.
	 */
	public function __construct(
		private UserCredentialsLookupInterface $credentialLookup,
		private PasswordHasherInterface $passwordHasher,
		private UserMustBeActive $userMustBeActive,
		private UserStatusTranslator $userStatusTranslator,
		private IssueChallenge $issueChallenge,
		private OpenSession $openSession,
		private ChallengeSecretDeliveryInterface $delivery
	) {}

	/**
	 * {@inheritDoc}
	 *
	 * @throws AuthenticationFailedException if the authentication fails
	 */
	public function authenticate(Request $request): ?AuthenticatedUser
	{
		if (!$request->has('email')) {
			throw AuthenticationFailedException::forMissingEmail();
		}

		if (!$request->has('password')) {
			throw AuthenticationFailedException::forMissingPassword();
		}

		$userCredentials = $this->userLookup($request->get('email'));

		$this->userMustBeActive->guard($this->userStatusTranslator->translate($userCredentials->status));

		if (!$this->passwordHasher->verify($request->get('password'), $userCredentials->passwordHash)) {
			throw AuthenticationFailedException::forInvalid();
		}

		$userId = UserId::fromString($userCredentials->id);

		if ($userCredentials->isTwoFactorEnabled) {
			$this->issueCode($userId);

			// Interrupt the authentication process to wait for the user to enter the code
			return null;
		}

		// Open a session for the user
		$session = $this->openSession($userId);

		return new AuthenticatedUser(
			$userId->toNative(),
			$userCredentials->email,
			$userCredentials->displayName,
			DateTime::now()->toNative(),
			$session->getAccessToken()->toNative(),
			$session->getRefreshToken()->toNative()
		);
	}

	/**
	 * Looks up the user.
	 *
	 * @throws AuthenticationFailedException if the user is not found
	 */
	private function userLookup(string $email): UserCredentials
	{
		$userCredentials = $this->credentialLookup->findByEmail($email);

		if (null === $userCredentials) {
			throw AuthenticationFailedException::userNotFound($email);
		}

		return $userCredentials;
	}

	/**
	 * Issues a challenge code for the user when the user enabled two-factor authentication.
	 */
	private function issueCode(UserId $userId): void
	{
		$challenge = $this->issueChallenge->issue($userId, ChallengeType::Code);

		/** @var ChallengeCode $challengeCode */
		$challengeCode = $challenge->getSecret();

		$this->delivery->deliver($userId, $challenge->getType()->value, $challengeCode->toNative());
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
