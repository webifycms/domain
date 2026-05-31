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

namespace Webify\User\Authentication\Domain\Guard;

use Webify\User\Authentication\Domain\Exception\AuthenticationFailedException;
use Webify\User\Authentication\Domain\ValueObject\UserStatus;

/**
 * Guard that ensures that the user is active.
 */
final class UserMustBeActive
{
	/**
	 * Ensures that the given status meets the required conditions.
	 *
	 * @param UserStatus $status the status to be checked
	 */
	public function guard(UserStatus $status): void
	{
		if (!$status->isActive()) {
			throw AuthenticationFailedException::userNotActive();
		}
	}
}
