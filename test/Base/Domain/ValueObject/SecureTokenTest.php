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

namespace Webify\Test\Base\Domain\ValueObject;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test, UsesClass};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\ValueObject\SecureToken;
use Webify\Test\Base\Domain\ValueObject\Example\ExampleSecureToken;

/**
 * Test class for a SecureToken value object.
 *
 * @internal
 */
#[CoversClass(SecureToken::class)]
#[CoversMethod(SecureToken::class, 'fromString')]
#[CoversMethod(SecureToken::class, 'generate')]
#[CoversMethod(SecureToken::class, 'toNative')]
#[CoversMethod(SecureToken::class, 'equals')]
#[CoversMethod(SecureToken::class, '__toString')]
#[UsesClass(ExampleSecureToken::class)]
final class SecureTokenTest extends TestCase
{
	/**
	 * Test creating a valid secure token from a 64-character string.
	 */
	#[Test]
	public function testCreateValidSecureToken(): void
	{
		$token = $this->createConcreteSecureToken('0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');

		$this->assertInstanceOf(SecureToken::class, $token);
	}

	/**
	 * Test invalid short token throws exception.
	 */
	#[Test]
	public function testInvalidShortTokenThrowsException(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->createConcreteSecureToken('too-short');
	}

	/**
	 * Test generate produces a token of at least 64 characters.
	 */
	#[Test]
	public function testGenerateProducesLongEnoughToken(): void
	{
		$token = ExampleSecureToken::generate();

		$this->assertGreaterThanOrEqual(64, strlen($token->toNative()));
	}

	/**
	 * Test generate produces a hex string.
	 */
	#[Test]
	public function testGeneratedTokenIsHex(): void
	{
		$token = ExampleSecureToken::generate();

		$this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token->toNative());
	}

	/**
	 * Test generate produces unique tokens across calls.
	 */
	#[Test]
	public function testGenerateProducesUniqueTokens(): void
	{
		$token1 = ExampleSecureToken::generate();
		$token2 = ExampleSecureToken::generate();

		$this->assertNotSame($token1->toNative(), $token2->toNative());
	}

	/**
	 * Test fromString factory method.
	 */
	#[Test]
	public function testFromStringFactoryMethod(): void
	{
		$value = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token = $this->createConcreteSecureTokenFromString($value);

		$this->assertSame($value, $token->toNative());
	}

	/**
	 * Test to string conversion.
	 */
	#[Test]
	public function testToStringConversion(): void
	{
		$value = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token = $this->createConcreteSecureToken($value);

		$this->assertSame($value, (string) $token);
	}

	/**
	 * Test to native method.
	 */
	#[Test]
	public function testToNativeMethod(): void
	{
		$value = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token = $this->createConcreteSecureToken($value);

		$this->assertSame($value, $token->toNative());
	}

	/**
	 * Test equals with same values.
	 */
	#[Test]
	public function testEqualsWithSameValues(): void
	{
		$value  = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
		$token1 = $this->createConcreteSecureToken($value);
		$token2 = $this->createConcreteSecureToken($value);

		$this->assertTrue($token1->equals($token2));
	}

	/**
	 * Test equals with different values.
	 */
	#[Test]
	public function testEqualsWithDifferentValues(): void
	{
		$token1 = $this->createConcreteSecureToken('0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');
		$token2 = $this->createConcreteSecureToken('0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdee');

		$this->assertFalse($token1->equals($token2));
	}

	/**
	 * Test fromString returns the correct type (static binding).
	 */
	#[Test]
	public function testFromStringReturnsCorrectType(): void
	{
		$token = ExampleSecureToken::fromString('0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');

		$this->assertInstanceOf(ExampleSecureToken::class, $token);
	}

	/**
	 * Test generate returns the correct type (static binding).
	 */
	#[Test]
	public function testGenerateReturnsCorrectType(): void
	{
		$token = ExampleSecureToken::generate();

		$this->assertInstanceOf(ExampleSecureToken::class, $token);
	}

	/**
	 * Create a concrete implementation of SecureToken for testing.
	 */
	private function createConcreteSecureToken(string $value): ExampleSecureToken
	{
		return new ExampleSecureToken($value);
	}

	/**
	 * Create a concrete implementation of SecureToken using fromString factory method.
	 */
	private function createConcreteSecureTokenFromString(string $value): ExampleSecureToken
	{
		return ExampleSecureToken::fromString($value);
	}
}
