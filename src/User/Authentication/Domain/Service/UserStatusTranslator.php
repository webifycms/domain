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

use Webify\User\Authentication\Domain\ValueObject\UserStatus;

/**
 * UserStatusTranslator service handles the translation of the user status.
 *
 * Translates the user status string to a UserStatus enum value.
 */
final class UserStatusTranslator
{
	/**
	 * Translates a given user status string to a corresponding Authentication known status.
	 *
	 * @param string $status the user-provided status input, such as 'active'
	 *
	 * @return UserStatus the translated status corresponding to the input, or an empty string if no match is found
	 */
	public function translate(string $status): UserStatus
	{
		return match ($status) {
			'active' => UserStatus::Active,
			default  => UserStatus::Unverified,
		};
	}
}
