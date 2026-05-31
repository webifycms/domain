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
use Webify\User\Authentication\Domain\Exception\InvalidRefreshTokenException;
use Webify\User\Authentication\Domain\ValueObject\RefreshToken;

/**
 * Tests for the RefreshToken value object.
 *
 * @internal
 */
#[CoversClass(RefreshToken::class)]
#[CoversMethod(RefreshToken::class, '__toString')]
#[CoversMethod(RefreshToken::class, 'generate')]
#[CoversMethod(RefreshToken::class, 'throwExceptionWhenInvalid')]
final class RefreshTokenTest extends TestCase
{
	/**
	 * Test creating a valid refresh token from a 64-character hex string.
	 */
	#[Test]
	public function testFromStringWithValidToken(): void
	{
		$value = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token = RefreshToken::fromString($value);

		$this->assertSame($value, $token->toNative());
	}

	/**
	 * Test throws InvalidRefreshTokenException for a token shorter than 64 characters.
	 */
	#[Test]
	public function testThrowsExceptionForTooShortToken(): void
	{
		$this->expectException(InvalidRefreshTokenException::class);
		$this->expectExceptionMessage('The refresh token "short" is invalid.');

		RefreshToken::fromString('short');
	}

	/**
	 * Test generate always produces a refresh token with at least 64 characters.
	 */
	#[Test]
	public function testGenerateProducesLongEnoughToken(): void
	{
		$token = RefreshToken::generate();

		$this->assertGreaterThanOrEqual(64, strlen($token->toNative()));
	}

	/**
	 * Test generate returns a RefreshToken instance.
	 */
	#[Test]
	public function testGenerateReturnsRefreshTokenInstance(): void
	{
		$token = RefreshToken::generate();

		$this->assertInstanceOf(RefreshToken::class, $token);
	}

	/**
	 * Test fromString returns a RefreshToken instance.
	 */
	#[Test]
	public function testFromStringReturnsRefreshTokenInstance(): void
	{
		$token = RefreshToken::fromString('0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');

		$this->assertInstanceOf(RefreshToken::class, $token);
	}

	/**
	 * Test __toString returns the token value.
	 */
	#[Test]
	public function testToStringReturnsTokenValue(): void
	{
		$value = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token = RefreshToken::fromString($value);

		$this->assertSame($value, (string) $token);
	}

	/**
	 * Test equals returns true for two refresh tokens with the same value.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValue(): void
	{
		$value   = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token1  = RefreshToken::fromString($value);
		$token2  = RefreshToken::fromString($value);

		$this->assertTrue($token1->equals($token2));
	}

	/**
	 * Test equals returns false for two refresh tokens with different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$token1 = RefreshToken::generate();
		$token2 = RefreshToken::generate();

		$this->assertFalse($token1->equals($token2));
	}
}
