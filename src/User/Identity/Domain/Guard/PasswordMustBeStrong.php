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

namespace Webify\User\Identity\Domain\Guard;

use Webify\User\Identity\Domain\Exception\WeakPasswordException;
use Webify\User\Identity\Domain\Policy\PasswordPolicy;

/**
 * Guard prevents against the weak password.
 */
final readonly class PasswordMustBeStrong
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private PasswordPolicy $policy
	) {}

	/**
	 * Validates the provided password against the defined password policy.
	 *
	 * @param string $password the password to be validated
	 *
	 * @throws WeakPasswordException If the password does not meet the policy requirements:
	 *                               - Too short
	 *                               - Too long
	 *                               - Missing uppercase character
	 *                               - Missing lowercase character
	 *                               - Missing digit
	 *                               - Missing special character
	 */
	public function guard(string $password): void
	{
		$length = mb_strlen($password);

		if (!$this->policy->isSatisfiedMinimumLength($length)) {
			throw WeakPasswordException::tooShort($length);
		}

		if (!$this->policy->isSatisfiedMaximumLength($length)) {
			throw WeakPasswordException::tooLong($length);
		}

		if (!$this->policy->isSatisfiedUppercase($password)) {
			throw WeakPasswordException::missingUppercase();
		}

		if (!$this->policy->isSatisfiedLowercase($password)) {
			throw WeakPasswordException::missingLowercase();
		}

		if (!$this->policy->isSatisfiedDigit($password)) {
			throw WeakPasswordException::missingDigit();
		}

		if (!$this->policy->isSatisfiedSpecialCharacter($password)) {
			throw WeakPasswordException::missingSpecialChar();
		}
	}
}
