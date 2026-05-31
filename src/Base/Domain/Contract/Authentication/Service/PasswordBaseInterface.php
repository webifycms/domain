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

namespace Webify\Base\Domain\Contract\Authentication\Service;

use Webify\Base\Domain\Contract\Authentication\{AuthenticatedUser, Credentials};

/**
 * The service defines the contract for password base authentication,
 * which handles email and password authentication flow.
 *
 * If the user has 2FA enabled, the service validates the password but does
 * not return the successful authenticated user — it issues an OTP challenge and returns nil.
 * The second step is completed via ChallengeBaseInterface::complete().
 */
interface PasswordBaseInterface
{
	/**
	 * Authenticates a user based on the provided credentials.
	 * 1. It delegates to the matched strategy.
	 * 2. It returns either a session (single-step) or a pending challenge descriptor (multistep).
	 *
	 * @param Credentials $request the credentials used for authentication request
	 */
	public function authenticate(Credentials $request): ?AuthenticatedUser;
}
