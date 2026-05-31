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
use Webify\User\Authentication\Domain\Exception\InvalidChallengeCodeException;
use Webify\User\Authentication\Domain\ValueObject\ChallengeCode;

/**
 * Tests for the ChallengeCode value object.
 *
 * @internal
 */
#[CoversClass(ChallengeCode::class)]
#[CoversMethod(ChallengeCode::class, '__toString')]
#[CoversMethod(ChallengeCode::class, 'fromNative')]
#[CoversMethod(ChallengeCode::class, 'fromString')]
#[CoversMethod(ChallengeCode::class, 'generate')]
#[CoversMethod(ChallengeCode::class, 'toNative')]
#[CoversMethod(ChallengeCode::class, 'equals')]
final class ChallengeCodeTest extends TestCase
{
	/**
	 * Test creating a valid challenge code from a native string.
	 */
	#[Test]
	public function testFromNativeWithValidCode(): void
	{
		$code = ChallengeCode::fromNative('123456');

		$this->assertSame('123456', $code->toNative());
	}

	/**
	 * Test creating a valid challenge code from a string.
	 */
	#[Test]
	public function testFromStringWithValidCode(): void
	{
		$code = ChallengeCode::fromString('123456');

		$this->assertSame('123456', $code->toNative());
	}

	/**
	 * Test fromNative pads short strings to six digits.
	 */
	#[Test]
	public function testFromNativePadsShortString(): void
	{
		$code = ChallengeCode::fromNative('123');

		$this->assertSame('000123', $code->toNative());
	}

	/**
	 * Test throws InvalidChallengeCodeException for a string with invalid length.
	 */
	#[Test]
	public function testThrowsExceptionForStringWithInvalidLength(): void
	{
		$this->expectException(InvalidChallengeCodeException::class);
		ChallengeCode::fromString('123');
	}

	/**
	 * Test throws InvalidChallengeCodeException for an empty string.
	 */
	#[Test]
	public function testThrowsExceptionForEmptyString(): void
	{
		$this->expectException(InvalidChallengeCodeException::class);
		ChallengeCode::fromString('');
	}

	/**
	 * Test creating a challenge code from a string with leading zeros.
	 */
	#[Test]
	public function testFromStringPreservesLeadingZeros(): void
	{
		$code = ChallengeCode::fromString('001234');

		$this->assertSame('001234', $code->toNative());
		$this->assertSame(6, strlen($code->toNative()));
	}

	/**
	 * Test generate returns a ChallengeCode instance.
	 */
	#[Test]
	public function testGenerateReturnsChallengeCodeInstance(): void
	{
		$this->assertInstanceOf(ChallengeCode::class, ChallengeCode::generate());
	}

	/**
	 * Test __toString returns the string representation of the code.
	 */
	#[Test]
	public function testToStringReturnsStringRepresentation(): void
	{
		$code = ChallengeCode::fromNative('123456');

		$this->assertSame('123456', (string) $code);
	}

	/**
	 * Test equals returns true for two challenge codes with the same value.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValue(): void
	{
		$code1 = ChallengeCode::fromNative('123456');
		$code2 = ChallengeCode::fromNative('123456');

		$this->assertTrue($code1->equals($code2));
	}

	/**
	 * Test equals returns false for two challenge codes with different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$code1 = ChallengeCode::generate();
		$code2 = ChallengeCode::generate();

		$this->assertFalse($code1->equals($code2));
	}
}
