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
 * The challenge type value object.
 *
 * Enumerates the 2FA authentication challenge types the domain supports natively.
 */
enum ChallengeType: string
{
	case Code = 'code';
	case Link = 'link';

	/**
	 * Determines if the current instance represents a Code.
	 */
	public function isCode(): bool
	{
		return self::Code === $this;
	}

	/**
	 * Determines if the current instance represents a Link.
	 */
	public function isLink(): bool
	{
		return self::Link === $this;
	}
}
