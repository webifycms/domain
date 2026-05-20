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

namespace Webify\User\Identity\Domain\Policy;

/**
 * Represents a password policy that validates passwords against specific criteria.
 * This class defines rules such as minimum and maximum length, inclusion of uppercase
 * and lowercase letters, digits, and special characters.
 */
final readonly class PasswordPolicy
{
	/**
	 * The constructor.
	 *
	 * @param int $minimumLength         minimal length of the password
	 * @param int $maximumLength         maximum length of the password
	 * @param int $minimumUppercaseChars minimum number of uppercase characters in the password
	 * @param int $minimumLowercaseChars minimum number of lowercase characters in the password
	 * @param int $minimumDigits         minimum number of digits in the password
	 * @param int $minimumSpecialChars   minimum number of special characters in the password
	 */
	public function __construct(
		private int $minimumLength = 12,
		private int $maximumLength = 128,
		private int $minimumUppercaseChars = 2,
		private int $minimumLowercaseChars = 2,
		private int $minimumDigits = 1,
		private int $minimumSpecialChars = 1
	) {}

	/**
	 * Checks if the given length satisfies the minimum required length.
	 *
	 * @param int $length the length to be checked
	 *
	 * @return bool true if the length is greater than or equal to the minimum required length, false otherwise
	 */
	public function isSatisfiedMinimumLength(int $length): bool
	{
		return $this->minimumLength <= $length;
	}

	/**
	 * Checks if the given length satisfies the maximum length constraint.
	 *
	 * @param int $length the length to be validated against the maximum allowed value
	 *
	 * @return bool returns true if the length is less than or equal to the maximum length, otherwise false
	 */
	public function isSatisfiedMaximumLength(int $length): bool
	{
		return $this->maximumLength >= $length;
	}

	/**
	 * Checks if the given password contains at least one uppercase letter.
	 *
	 * @param string $password the password to be validated for an uppercase letter
	 *
	 * @return bool returns true if the password contains at least one uppercase letter, otherwise false
	 */
	public function isSatisfiedUppercase(string $password): bool
	{
		return preg_match_all('/[A-Z]/', $password) >= $this->minimumUppercaseChars;
	}

	/**
	 * Determines if the given password contains at least one lowercase letter.
	 *
	 * @param string $password the password to check for the presence of lowercase letters
	 *
	 * @return bool returns true if the password contains one or more lowercase letters, otherwise false
	 */
	public function isSatisfiedLowercase(string $password): bool
	{
		return preg_match_all('/[a-z]/', $password) >= $this->minimumLowercaseChars;
	}

	/**
	 * Determines if the given password contains at least one digit.
	 *
	 * @param string $password the password to be checked for the presence of a digit
	 *
	 * @return bool returns true if the password contains at least one digit, otherwise false
	 */
	public function isSatisfiedDigit(string $password): bool
	{
		return preg_match_all('/\d/', $password) >= $this->minimumDigits;
	}

	/**
	 * Checks if the given password contains at least one special character.
	 *
	 * @param string $password the password to evaluate
	 *
	 * @return bool returns true if the password contains a special character, otherwise false
	 */
	public function isSatisfiedSpecialCharacter(string $password): bool
	{
		return preg_match_all('/[\W_]/', $password) >= $this->minimumSpecialChars;
	}
}
