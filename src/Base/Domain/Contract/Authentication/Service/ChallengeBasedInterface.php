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

use Webify\Base\Domain\Contract\Authentication\{AuthenticatedUser, Request};

/**
 * The service defines the contract for authenticating users.
 *
 * Authentication service is the single entry point for all authentication flows.
 *
 * It orchestrates two operations:
 *   initiate() — starts authentication, delegates to the matched strategy.
 *                Returns either a session (single-step) or a pending challenge
 *                descriptor (multistep).
 *
 *   complete() — finishes a challenge-based flow. All challenge types
 *                (OTP, magic link, 2FA) converge here regardless of how
 *                the challenge was initiated.
 *
 * The service holds no state of its own and knows nothing about individual
 * strategies or challenge mechanics. Adding a new strategy or changing TTLs
 * requires no changes here.
 */
interface ChallengeBasedInterface
{
	/**
	 * Initiate a passwordless (challenged-based) authentication flow. It delegates to the matched strategy.
	 *
	 * @param Request $credentials the credentials used for authentication request
	 */
	public function initiate(Request $credentials): void;

	/**
	 * Completes a passwordless (challenged-based) authentication flow.
	 *
	 * @param Request $credentials the credentials used for authentication request
	 *
	 * @return AuthenticatedUser returns the successfully authenticated user
	 */
	public function complete(Request $credentials): AuthenticatedUser;
}
