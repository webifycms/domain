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
use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\ValueObject\Email;

/**
 * EmailTest tests the functionality of the Email class.
 *
 * @internal
 */
#[CoversClass(Email::class)]
#[CoversMethod(Email::class, 'fromString')]
#[CoversMethod(Email::class, 'toNative')]
final class EmailTest extends TestCase
{
	#[Test]
	public function testFromStringCreatesInstance(): void
	{
		$email = ExampleEmail::fromString('test@example.com');

		$this->assertInstanceOf(ExampleEmail::class, $email);
	}

	#[Test]
	public function testFromStringConvertsToLowercase(): void
	{
		$email = ExampleEmail::fromString('TEST@EXAMPLE.COM');

		$this->assertSame('test@example.com', $email->toNative());
	}

	#[Test]
	public function testFromStringTrimsWhitespace(): void
	{
		$email = ExampleEmail::fromString('  test@example.com  ');

		$this->assertSame('test@example.com', $email->toNative());
	}

	#[Test]
	public function testToNativeReturnsEmailString(): void
	{
		$email    = ExampleEmail::fromString('test@example.com');

		$this->assertSame('test@example.com', $email->toNative());
	}

	#[Test]
	public function testToStringMagicMethod(): void
	{
		$email = ExampleEmail::fromString('test@example.com');

		$this->assertSame('test@example.com', (string) $email);
	}

	#[Test]
	public function testValidEmailWithCommonDomain(): void
	{
		$email = ExampleEmail::fromString('user@gmail.com');

		$this->assertSame('user@gmail.com', $email->toNative());
	}

	#[Test]
	public function testValidEmailWithSubdomain(): void
	{
		$email = ExampleEmail::fromString('user@mail.example.com');

		$this->assertSame('user@mail.example.com', $email->toNative());
	}

	#[Test]
	public function testValidEmailWithPlusAddressing(): void
	{
		$email = ExampleEmail::fromString('user+tag@example.com');

		$this->assertSame('user+tag@example.com', $email->toNative());
	}

	#[Test]
	public function testValidEmailWithDotInLocalPart(): void
	{
		$email = ExampleEmail::fromString('first.last@example.com');

		$this->assertSame('first.last@example.com', $email->toNative());
	}

	#[Test]
	public function testFromStringThrowsForEmptyValue(): void
	{
		$this->expectException(InvalidArgumentException::class);

		ExampleEmail::fromString('');
	}

	#[Test]
	public function testFromStringThrowsForWhitespaceOnly(): void
	{
		$this->expectException(InvalidArgumentException::class);

		ExampleEmail::fromString('   ');
	}

	#[Test]
	public function testFromStringThrowsForInvalidEmailFormat(): void
	{
		$this->expectException(InvalidArgumentException::class);

		ExampleEmail::fromString('not-an-email');
	}

	#[Test]
	public function testFromStringThrowsForMissingAtSymbol(): void
	{
		$this->expectException(InvalidArgumentException::class);

		ExampleEmail::fromString('example.com');
	}

	#[Test]
	public function testFromStringThrowsForMissingLocalPart(): void
	{
		$this->expectException(InvalidArgumentException::class);

		ExampleEmail::fromString('@example.com');
	}

	#[Test]
	public function testFromStringThrowsForMissingDomain(): void
	{
		$this->expectException(InvalidArgumentException::class);

		ExampleEmail::fromString('test@');
	}

	#[Test]
	public function testFromStringThrowsForEmailExceedingMaxLength(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$longLocalPart = str_repeat('a', 250);
		ExampleEmail::fromString($longLocalPart . '@example.com');
	}

	#[Test]
	public function testBlockedDomainTempmail(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Disposable email addresses are not allowed.');

		ExampleEmail::fromString('test@tempmail.com');
	}

	#[Test]
	public function testBlockedDomainMailinator(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Disposable email addresses are not allowed.');

		ExampleEmail::fromString('test@mailinator.com');
	}

	#[Test]
	public function testBlockedDomainGuerrillamail(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Disposable email addresses are not allowed.');

		ExampleEmail::fromString('test@guerrillamail.com');
	}

	#[Test]
	public function testEmailValueObjectEquality(): void
	{
		$email1 = ExampleEmail::fromString('test@example.com');
		$email2 = ExampleEmail::fromString('test@example.com');

		$this->assertSame($email1->toNative(), $email2->toNative());
	}
}
