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

namespace Webify\Test\User\Authentication\Domain\ValueObject;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authentication\Domain\Exception\InvalidChallengeTokenException;
use Webify\User\Authentication\Domain\ValueObject\ChallengeToken;

/**
 * Tests for the ChallengeToken value object.
 *
 * @internal
 */
#[CoversClass(ChallengeToken::class)]
#[CoversMethod(ChallengeToken::class, '__toString')]
#[CoversMethod(ChallengeToken::class, 'generate')]
#[CoversMethod(ChallengeToken::class, 'throwExceptionWhenInvalid')]
final class ChallengeTokenTest extends TestCase
{
	/**
	 * Test creating a valid challenge token from a 64-character hex string.
	 */
	#[Test]
	public function testFromStringWithValidToken(): void
	{
		$value = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token = ChallengeToken::fromString($value);

		$this->assertSame($value, $token->toNative());
	}

	/**
	 * Test throws InvalidChallengeTokenException for a token shorter than 64 characters.
	 */
	#[Test]
	public function testThrowsExceptionForTooShortToken(): void
	{
		$this->expectException(InvalidChallengeTokenException::class);
		$this->expectExceptionMessage('The authentication challenge token "short" is invalid.');

		ChallengeToken::fromString('short');
	}

	/**
	 * Test generate always produces a challenge token with at least 64 characters.
	 */
	#[Test]
	public function testGenerateProducesLongEnoughToken(): void
	{
		$token = ChallengeToken::generate();

		$this->assertGreaterThanOrEqual(64, strlen($token->toNative()));
	}

	/**
	 * Test generate returns a ChallengeToken instance.
	 */
	#[Test]
	public function testGenerateReturnsChallengeTokenInstance(): void
	{
		$token = ChallengeToken::generate();

		$this->assertInstanceOf(ChallengeToken::class, $token);
	}

	/**
	 * Test fromString returns a ChallengeToken instance.
	 */
	#[Test]
	public function testFromStringReturnsChallengeTokenInstance(): void
	{
		$token = ChallengeToken::fromString('0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');

		$this->assertInstanceOf(ChallengeToken::class, $token);
	}

	/**
	 * Test __toString returns the token value.
	 */
	#[Test]
	public function testToStringReturnsTokenValue(): void
	{
		$value = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token = ChallengeToken::fromString($value);

		$this->assertSame($value, (string) $token);
	}

	/**
	 * Test equals returns true for two challenge tokens with the same value.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValue(): void
	{
		$value  = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token1 = ChallengeToken::fromString($value);
		$token2 = ChallengeToken::fromString($value);

		$this->assertTrue($token1->equals($token2));
	}

	/**
	 * Test equals returns false for two challenge tokens with different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$token1 = ChallengeToken::generate();
		$token2 = ChallengeToken::generate();

		$this->assertFalse($token1->equals($token2));
	}
}
