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
use Webify\User\Authentication\Domain\Exception\InvalidSessionIdException;
use Webify\User\Authentication\Domain\ValueObject\SessionId;

/**
 * Tests for the TokenId value object.
 *
 * @internal
 */
#[CoversClass(SessionId::class)]
#[CoversMethod(SessionId::class, 'fromString')]
#[CoversMethod(SessionId::class, 'throwException')]
final class SessionIdTest extends TestCase
{
	/**
	 * Test can create a valid token ID.
	 */
	#[Test]
	public function testValidTokenId(): void
	{
		$validId = '01F8MECHZX3TBDSZ7XRADM79XV';
		$tokenId = SessionId::fromString($validId);

		$this->assertSame($validId, $tokenId->toNative());
	}

	/**
	 * Test throws an exception for invalid token ID.
	 */
	#[Test]
	public function testItThrowsExceptionForInvalidTokenId(): void
	{
		$this->expectException(InvalidSessionIdException::class);
		$this->expectExceptionMessage('The session ID "invalid-session-id" is invalid.');

		SessionId::fromString('invalid-session-id');
	}
}
