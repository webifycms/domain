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
use Webify\User\Authentication\Domain\ValueObject\ChallengeType;

/**
 * ChallengeTypeTest tests the functionality of the ChallengeType enum.
 *
 * @internal
 */
#[CoversClass(ChallengeType::class)]
#[CoversMethod(ChallengeType::class, 'isCode')]
#[CoversMethod(ChallengeType::class, 'isLink')]
final class ChallengeTypeTest extends TestCase
{
	/**
	 * Tests that the Code case has the correct backing value.
	 */
	#[Test]
	public function testCodeCaseHasCorrectValue(): void
	{
		$this->assertSame('code', ChallengeType::Code->value);
	}

	/**
	 * Tests that the Link case has the correct backing value.
	 */
	#[Test]
	public function testLinkCaseHasCorrectValue(): void
	{
		$this->assertSame('link', ChallengeType::Link->value);
	}

	/**
	 * Tests that isCode returns true for the Code case and false for the Link case.
	 */
	#[Test]
	public function testIsCode(): void
	{
		$this->assertTrue(ChallengeType::Code->isCode());
		$this->assertFalse(ChallengeType::Link->isCode());
	}

	/**
	 * Tests that isLink returns true for the Link case and false for the Code case.
	 */
	#[Test]
	public function testIsLink(): void
	{
		$this->assertTrue(ChallengeType::Link->isLink());
		$this->assertFalse(ChallengeType::Code->isLink());
	}

	/**
	 * Tests that tryFrom returns the correct case for a valid value.
	 */
	#[Test]
	public function testTryFromWithValidValue(): void
	{
		$this->assertSame(ChallengeType::Code, ChallengeType::tryFrom('code'));
		$this->assertSame(ChallengeType::Link, ChallengeType::tryFrom('link'));
	}

	/**
	 * Tests that tryFrom returns null for an invalid value.
	 */
	#[Test]
	public function testTryFromWithInvalidValue(): void
	{
		// @phpstan-ignore-next-line
		$this->assertNull(ChallengeType::tryFrom('invalid'));
	}
}
