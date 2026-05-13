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

/**
 * Data transfer object to hold user information.
 */
final readonly class UserCredential
{
	/**
	 * Constructor for initializing the class with user information.
	 *
	 * @param string $id           the identifier of the user
	 * @param string $email        the email address of the user
	 * @param string $passwordHash the hashed password of the user
	 * @param string $userStatus   the status of the user
	 */
	public function __construct(
		public string $id,
		public string $email,
		public string $passwordHash,
		public string $userStatus
	) {}
}
