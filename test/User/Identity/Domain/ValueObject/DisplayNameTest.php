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

namespace Webify\Test\User\Identity\Domain\ValueObject;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Identity\Domain\Exception\InvalidDisplayNameException;
use Webify\User\Identity\Domain\ValueObject\DisplayName;

/**
 * DisplayNameTest tests the functionality of the DisplayName value object.
 *
 * @internal
 */
#[CoversClass(DisplayName::class)]
#[CoversMethod(DisplayName::class, 'fromString')]
#[CoversMethod(DisplayName::class, 'equals')]
#[CoversMethod(DisplayName::class, '__toString')]
#[CoversMethod(DisplayName::class, 'toNative')]
final class DisplayNameTest extends TestCase
{
	/**
	 * Tests the creation of a valid display name from a string.
	 */
	#[Test]
	public function testCreateValidDisplayName(): void
	{
		$displayName = DisplayName::fromString('Test User');

		$this->assertInstanceOf(DisplayName::class, $displayName);
		$this->assertSame('Test User', $displayName->toNative());
	}

	/**
	 * Tests that the display name value is trimmed of whitespace during creation.
	 */
	#[Test]
	public function testCreateDisplayNameTrimsValue(): void
	{
		$displayName = DisplayName::fromString('  Test User  ');

		$this->assertSame('Test User', $displayName->toNative());
	}

	/**
	 * Tests that creating a display name with fewer than the minimum allowed characters throws an exception.
	 */
	#[Test]
	public function testDisplayNameTooShortThrowsException(): void
	{
		$this->expectException(InvalidDisplayNameException::class);
		DisplayName::fromString('AB');
	}

	/**
	 * Tests that creating a display name with more than the maximum allowed characters throws an exception.
	 */
	#[Test]
	public function testDisplayNameTooLongThrowsException(): void
	{
		$this->expectException(InvalidDisplayNameException::class);
		DisplayName::fromString(str_repeat('A', 256));
	}

	/**
	 * Tests that creating a display name with an empty string throws an exception.
	 */
	#[Test]
	public function testEmptyDisplayNameThrowsException(): void
	{
		$this->expectException(InvalidDisplayNameException::class);
		DisplayName::fromString('');
	}

	/**
	 * Tests that creating a display name with only whitespace characters throws an exception.
	 */
	#[Test]
	public function testWhitespaceOnlyDisplayNameThrowsException(): void
	{
		$this->expectException(InvalidDisplayNameException::class);
		DisplayName::fromString('   ');
	}

	/**
	 * Tests the toNative method returns the string value.
	 */
	#[Test]
	public function testToNativeReturnsStringValue(): void
	{
		$displayName = DisplayName::fromString('Test User');

		$this->assertSame('Test User', $displayName->toNative());
	}

	/**
	 * Tests the string representation of the display name.
	 */
	#[Test]
	public function testStringRepresentation(): void
	{
		$displayName = DisplayName::fromString('Test User');

		$this->assertSame('Test User', (string) $displayName);
	}

	/**
	 * Tests that equals returns true for two display names with the same value.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValue(): void
	{
		$displayName1 = DisplayName::fromString('Test User');
		$displayName2 = DisplayName::fromString('Test User');

		$this->assertTrue($displayName1->equals($displayName2));
	}

	/**
	 * Tests that equals returns false for two display names with different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValue(): void
	{
		$displayName1 = DisplayName::fromString('User One');
		$displayName2 = DisplayName::fromString('User Two');

		$this->assertFalse($displayName1->equals($displayName2));
	}
}
