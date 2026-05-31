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
use Webify\User\Authentication\Domain\Exception\InvalidChallengeIdException;
use Webify\User\Authentication\Domain\ValueObject\ChallengeId;

/**
 * Tests for the ChallengeId value object.
 *
 * @internal
 */
#[CoversClass(ChallengeId::class)]
#[CoversMethod(ChallengeId::class, 'fromString')]
#[CoversMethod(ChallengeId::class, 'throwException')]
final class ChallengeIdTest extends TestCase
{
	/**
	 * Test can create a valid challenge ID.
	 */
	#[Test]
	public function testValidChallengeId(): void
	{
		$validId     = '01F8MECHZX3TBDSZ7XRADM79XV';
		$challengeId = ChallengeId::fromString($validId);

		$this->assertSame($validId, $challengeId->toNative());
	}

	/**
	 * Test throws an exception for invalid challenge ID.
	 */
	#[Test]
	public function testItThrowsExceptionForInvalidChallengeId(): void
	{
		$this->expectException(InvalidChallengeIdException::class);
		$this->expectExceptionMessage('The authentication challenge ID "invalid-challenge-id" is invalid.');
		ChallengeId::fromString('invalid-challenge-id');
	}
}
