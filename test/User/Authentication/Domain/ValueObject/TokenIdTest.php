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
use Webify\User\Authentication\Domain\Exception\InvalidTokenIdException;
use Webify\User\Authentication\Domain\ValueObject\TokenId;

/**
 * Tests for the TokenId value object.
 *
 * @internal
 */
#[CoversClass(TokenId::class)]
#[CoversMethod(TokenId::class, 'fromString')]
#[CoversMethod(TokenId::class, 'throwException')]
final class TokenIdTest extends TestCase
{
	/**
	 * Test can create valid token ID.
	 */
	#[Test]
	public function testValidTokenId(): void
	{
		$validId = '01F8MECHZX3TBDSZ7XRADM79XV';
		$tokenId = TokenId::fromString($validId);

		$this->assertSame($validId, $tokenId->toNative());
	}

	/**
	 * Test throws exception for invalid token ID.
	 */
	#[Test]
	public function testItThrowsExceptionForInvalidTokenId(): void
	{
		$this->expectException(InvalidTokenIdException::class);
		$this->expectExceptionMessage('The token ID "invalid-token-id" is invalid.');

		TokenId::fromString('invalid-token-id');
	}
}
