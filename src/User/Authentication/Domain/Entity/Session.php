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
use Webify\User\Authentication\Domain\Event\{SessionWasOpened, SessionWasRefreshed, SessionWasRevoked};
use Webify\User\Authentication\Domain\Exception\CannotRefreshSessionException;
use Webify\User\Authentication\Domain\ValueObject\{
	AccessToken,
	RefreshToken,
	SessionId,
	UserId
};

/**
 * Session aggregate root.
 *
 * Represents the lifecycle of a single authenticated user session. A Session holds an AccessToken,
 * a RefreshToken, a reference to the authenticated user, and optional expiry. It encapsulates
 * the business rules around token rotation and session expiry.
 */
final class Session extends AggregateRoot
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this aggregate root.
	 *
	 * @param SessionId    $id           unique session identifier
	 * @param UserId       $userId       user's identifier
	 * @param AccessToken  $accessToken  token used for accessing resources
	 * @param RefreshToken $refreshToken token used to refresh expired access tokens
	 * @param DateTime     $expiresAt    date and time when the session expires
	 * @param DateTime     $createdAt    date and time when the session was created
	 * @param bool         $revoked      indicates whether the session has been revoked. Defaults to false.
	 */
	private function __construct(
		private readonly SessionId $id,
		private readonly UserId $userId,
		private AccessToken $accessToken,
		private RefreshToken $refreshToken,
		private DateTime $expiresAt,
		private readonly DateTime $createdAt,
		private bool $revoked = false
	) {}

	/**
	 * Get the ID.
	 */
	public function getId(): SessionId
	{
		return $this->id;
	}

	/**
	 * Get the user ID.
	 */
	public function getUserId(): UserId
	{
		return $this->userId;
	}

	/**
	 * Get the access token.
	 */
	public function getAccessToken(): AccessToken
	{
		return $this->accessToken;
	}

	/**
	 * Get the refresh token.
	 */
	public function getRefreshToken(): RefreshToken
	{
		return $this->refreshToken;
	}

	/**
	 * Get the expiration date of the session.
	 */
	public function getExpiresAt(): DateTime
	{
		return $this->expiresAt;
	}

	/**
	 * Get the creation date of the session.
	 */
	public function getCreatedAt(): DateTime
	{
		return $this->createdAt;
	}

	/**
	 * Refresh the session with new access and refresh tokens and extend expiry.
	 */
	public function refresh(AccessToken $newAccessToken, RefreshToken $newRefreshToken, DateTime $newExpireAt): void
	{
		if ($this->isExpired() || $this->isRevoked()) {
			throw CannotRefreshSessionException::create();
		}

		$this->accessToken  = $newAccessToken;
		$this->refreshToken = $newRefreshToken;
		$this->expiresAt    = $newExpireAt;

		$this->recordDomainEvent(
			new SessionWasRefreshed(
				$this->id->toNative(),
				$this->userId->toNative(),
				$this->expiresAt->toNative(),
				$this->createdAt->toNative()
			)
		);
	}

	/**
	 * Revoke the session.
	 */
	public function revoke(): void
	{
		// Prevent revocation if the session has already been revoked (idempotent behaviour).
		if ($this->revoked) {
			return;
		}

		$this->revoked = true;

		$this->recordDomainEvent(
			new SessionWasRevoked(
				$this->id->toNative(),
				$this->userId->toNative(),
				$this->expiresAt->toNative(),
				$this->createdAt->toNative()
			)
		);
	}

	/**
	 * Check if the session has expired.
	 */
	public function isExpired(): bool
	{
		return DateTime::now() > $this->expiresAt;
	}

	/**
	 * Check if the session is active.
	 */
	public function isActive(): bool
	{
		return !$this->isExpired() && !$this->isRevoked();
	}

	/**
	 * Check if the session has been revoked.
	 */
	public function isRevoked(): bool
	{
		return $this->revoked;
	}

	/**
	 * Open a new session with the provided details.
	 *
	 * @param SessionId    $id           the unique identifier for the session
	 * @param UserId       $userId       the user's unique identifier
	 * @param AccessToken  $accessToken  the access token for the session
	 * @param RefreshToken $refreshToken the refresh token for the session
	 * @param DateTime     $expiresAt    the expiration date and time for the session
	 *
	 * @return Session a new session instance
	 */
	public static function open(
		SessionId $id,
		UserId $userId,
		AccessToken $accessToken,
		RefreshToken $refreshToken,
		DateTime $expiresAt
	): Session {
		$session = new self($id, $userId, $accessToken, $refreshToken, $expiresAt, DateTime::now());

		$session->recordDomainEvent(
			new SessionWasOpened(
				$session->getId()->toNative(),
				$session->getUserId()->toNative(),
				$session->getExpiresAt()->toNative(),
				$session->getCreatedAt()->toNative()
			)
		);

		return $session;
	}
}
