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

namespace Webify\User\Authentication\Domain\Query\Session;

/**
 * Session read model.
 */
final readonly class Session
{
	/**
	 * The constructor.
	 */
	public function __construct(
		public string $id,
		public string $userId,
		public string $accessToken,
		public string $refreshToken,
		public string $expiresAt,
		public string $createdAt,
		public bool $revoked
	) {}
}
