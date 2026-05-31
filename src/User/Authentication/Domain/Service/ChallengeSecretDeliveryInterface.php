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

use Webify\User\Authentication\Domain\ValueObject\UserId;

/**
 * The service defines the contract for delivering the authentication challenge secret.
 */
interface ChallengeSecretDeliveryInterface
{
	/**
	 * Delivers the authentication challenge secret to the user's email address.
	 *
	 * @param UserId $userId          the user identifier
	 * @param string $challengeType   the authentication challenge type
	 * @param string $challengeSecret the authentication challenge secret
	 */
	public function deliver(UserId $userId, string $challengeType, string $challengeSecret): void;
}
