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
use Webify\User\Authentication\Domain\ValueObject\ChallengeStatus;

/**
 * Tests for the ChallengeStatus enum.
 *
 * @internal
 */
#[CoversClass(ChallengeStatus::class)]
#[CoversMethod(ChallengeStatus::class, 'isPending')]
#[CoversMethod(ChallengeStatus::class, 'isCompleted')]
final class ChallengeStatusTest extends TestCase
{
	/**
	 * Tests that the Pending case has the correct backing value.
	 */
	#[Test]
	public function testPendingCaseHasCorrectValue(): void
	{
		$this->assertSame('pending', ChallengeStatus::Pending->value);
	}

	/**
	 * Tests that the Completed case has the correct backing value.
	 */
	#[Test]
	public function testCompletedCaseHasCorrectValue(): void
	{
		$this->assertSame('completed', ChallengeStatus::Completed->value);
	}

	#[Test]
	public function testIsPendingReturnsTrueForPending(): void
	{
		$this->assertTrue(ChallengeStatus::Pending->isPending());
		$this->assertFalse(ChallengeStatus::Completed->isPending());
	}

	/**
	 * Tests that `isCompleted` returns true for the Completed case and false for the Pending case.
	 */
	#[Test]
	public function testIsCompletedReturnsTrueForCompleted(): void
	{
		$this->assertTrue(ChallengeStatus::Completed->isCompleted());
		$this->assertFalse(ChallengeStatus::Pending->isCompleted());
	}

	/**
	 * Tests that `tryFrom` returns the correct case for a valid value.
	 */
	#[Test]
	public function testTryFromWithValidValue(): void
	{
		$this->assertSame(ChallengeStatus::Pending, ChallengeStatus::tryFrom('pending'));
		$this->assertSame(ChallengeStatus::Completed, ChallengeStatus::tryFrom('completed'));
	}

	/**
	 * Tests that `tryFrom` returns null for an invalid value.
	 */
	#[Test]
	public function testTryFromWithInvalidValue(): void
	{
		// @phpstan-ignore-next-line
		$this->assertNull(ChallengeStatus::tryFrom('invalid'));
	}
}
