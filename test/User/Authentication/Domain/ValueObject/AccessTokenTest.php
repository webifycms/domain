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
use Webify\User\Authentication\Domain\Exception\InvalidAccessTokenException;
use Webify\User\Authentication\Domain\ValueObject\AccessToken;

/**
 * Tests for the AccessToken value object.
 *
 * @internal
 */
#[CoversClass(AccessToken::class)]
#[CoversMethod(AccessToken::class, 'fromString')]
#[CoversMethod(AccessToken::class, 'generate')]
#[CoversMethod(AccessToken::class, 'toNative')]
#[CoversMethod(AccessToken::class, 'equals')]
#[CoversMethod(AccessToken::class, '__toString')]
final class AccessTokenTest extends TestCase
{
	/**
	 * Test can create from a valid 64-character hex string.
	 */
	#[Test]
	public function testFromStringWithValidToken(): void
	{
		$value = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token = AccessToken::fromString($value);

		$this->assertSame($value, $token->toNative());
	}

	/**
	 * Test throws exception for token shorter than 64 characters.
	 */
	#[Test]
	public function testThrowsExceptionForTooShortToken(): void
	{
		$this->expectException(InvalidAccessTokenException::class);
		$this->expectExceptionMessage('The access token "short" is invalid.');

		AccessToken::fromString('short');
	}

	/**
	 * Test generate always produces a 64+ character token.
	 */
	#[Test]
	public function testGenerateProducesLongEnoughToken(): void
	{
		$token = AccessToken::generate();

		$this->assertGreaterThanOrEqual(64, strlen($token->toNative()));
	}

	/**
	 * Test generate produces a hex string.
	 */
	#[Test]
	public function testGeneratedTokenIsHex(): void
	{
		$token = AccessToken::generate();

		$this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token->toNative());
	}

	/**
	 * Test-generated tokens are unique across calls.
	 */
	#[Test]
	public function testGenerateProducesUniqueTokens(): void
	{
		$token1 = AccessToken::generate();
		$token2 = AccessToken::generate();

		$this->assertNotSame($token1->toNative(), $token2->toNative());
	}

	/**
	 * Test equals returns true for identical values.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValue(): void
	{
		$value  = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token1 = AccessToken::fromString($value);
		$token2 = AccessToken::fromString($value);

		$this->assertTrue($token1->equals($token2));
	}

	/**
	 * Test equals returns false for different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$token1 = AccessToken::generate();
		$token2 = AccessToken::generate();

		$this->assertFalse($token1->equals($token2));
	}

	/**
	 * Test __toString returns the token value.
	 */
	#[Test]
	public function testToStringReturnsTokenValue(): void
	{
		$value = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token = AccessToken::fromString($value);

		$this->assertSame($value, (string) $token);
	}
}
