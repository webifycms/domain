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

/**
 * The authentication challenge status value object.
 */
enum ChallengeStatus: string
{
	case Pending   = 'pending';
	case Completed = 'completed';

	/**
	 * Check if the challenge is pending.
	 */
	public function isPending(): bool
	{
		return self::Pending === $this;
	}

	/**
	 * Check if the challenge is completed.
	 */
	public function isCompleted(): bool
	{
		return self::Completed === $this;
	}
}
