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

namespace Webify\Test\User\Authorization\Domain\ValueObject;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authorization\Domain\Exception\InvalidSubjectTypeException;
use Webify\User\Authorization\Domain\ValueObject\SubjectType;

/**
 * SubjectTypeTest tests the functionality of the SubjectType value object.
 *
 * @internal
 */
#[CoversClass(SubjectType::class)]
#[CoversMethod(SubjectType::class, 'fromString')]
#[CoversMethod(SubjectType::class, 'equals')]
#[CoversMethod(SubjectType::class, '__toString')]
#[CoversMethod(SubjectType::class, 'toNative')]
final class SubjectTypeTest extends TestCase
{
	/**
	 * Tests the creation of a valid SubjectType instance and verifies its properties.
	 */
	#[Test]
	public function testCreateValidSubjectType(): void
	{
		$subjectType = new SubjectType('user');

		$this->assertInstanceOf(SubjectType::class, $subjectType);
		$this->assertSame('user', $subjectType->toNative());
	}

	/**
	 * Tests that an exception is thrown when attempting to create a SubjectType with an empty string.
	 */
	#[Test]
	public function testEmptySubjectTypeThrowsException(): void
	{
		$this->expectException(InvalidSubjectTypeException::class);
		new SubjectType('');
	}

	/**
	 * Tests that the toNative method of SubjectType returns the expected string value.
	 */
	#[Test]
	public function testToNativeReturnsStringValue(): void
	{
		$subjectType = new SubjectType('service');

		$this->assertSame('service', $subjectType->toNative());
	}

	/**
	 * Tests the string representation of a SubjectType to ensure it matches the expected value.
	 */
	#[Test]
	public function testStringRepresentation(): void
	{
		$subjectType = new SubjectType('system');

		$this->assertSame('system', (string) $subjectType);
	}

	/**
	 * Tests that the equals method returns true when comparing two SubjectType instances of the same type.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameType(): void
	{
		$subjectType1 = new SubjectType('user');
		$subjectType2 = new SubjectType('user');

		$this->assertTrue($subjectType1->equals($subjectType2));
	}

	/**
	 * Tests that the equals method returns false when comparing SubjectType instances of different types.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentType(): void
	{
		$subjectType1 = new SubjectType('user');
		$subjectType2 = new SubjectType('service');

		$this->assertFalse($subjectType1->equals($subjectType2));
	}
}
