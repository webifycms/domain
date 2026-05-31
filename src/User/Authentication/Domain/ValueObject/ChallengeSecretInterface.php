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
 * The challenge secret interface.
 */
interface ChallengeSecretInterface
{
	/**
	 * Verifies the challenge provided by the user against the stored challenge.
	 */
	public function verify(self $secret): bool;
}
