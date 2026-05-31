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

namespace Webify\User\Authentication\Domain\ValueObject;

use Webify\Base\Domain\ValueObject\DateTime;

/**
 * Authenticated user value object.
 *
 * Represents the authenticated user after successful authentication.
 */
final readonly class AuthenticatedUser
{
	/**
	 * Private constructor enforces the use of the factory method.
	 *
	 * @param UserId   $userId          authenticated user ID
	 * @param DateTime $authenticatedAt timestamp of successful authentication
	 */
	private function __construct(
		private UserId $userId,
		private DateTime $authenticatedAt,
	) {}

	/**
	 * Gets the authenticated user ID.
	 */
	public function getUserId(): UserId
	{
		return $this->userId;
	}

	/**
	 * Gets the date and time of successful authentication.
	 */
	public function getAuthenticatedAt(): DateTime
	{
		return $this->authenticatedAt;
	}

	/**
	 * Factory method to create an authenticated user.
	 *
	 * @param UserId $userId authenticated user ID
	 */
	public static function fromSuccessfulAuthentication(UserId $userId): self
	{
		return new self($userId, DateTime::now());
	}

	/**
	 * Converts the authenticated user to an array representation.
	 *
	 * @return array<string, string>
	 */
	public function toNative(): array
	{
		return [
			'user_id'          => $this->userId->toNative(),
			'authenticated_at' => $this->authenticatedAt->defaultFormat(),
		];
	}
}
