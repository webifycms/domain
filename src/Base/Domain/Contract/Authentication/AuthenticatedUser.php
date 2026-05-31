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

namespace Webify\Base\Domain\Contract\Authentication;

use DateTimeImmutable;

/**
 * AuthenticatedUser DTO represents an authenticated user.
 */
final readonly class AuthenticatedUser
{
	/**
	 * The constructor.
	 */
	public function __construct(
		public string $id,
		public string $email,
		public string $displayName,
		public DateTimeImmutable $authenticatedAt,
		public string $accessToken,
		public string $refreshToken
	) {}
}
