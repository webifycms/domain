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
use Webify\User\Authorization\Domain\Exception\InvalidSubjectIdException;
use Webify\User\Authorization\Domain\ValueObject\SubjectId;

/**
 * SubjectIdTest tests the functionality of the SubjectId value object.
 *
 * @internal
 */
#[CoversClass(SubjectId::class)]
#[CoversMethod(SubjectId::class, 'fromString')]
#[CoversMethod(SubjectId::class, 'toNative')]
#[CoversMethod(SubjectId::class, 'equals')]
#[CoversMethod(SubjectId::class, '__toString')]
final class SubjectIdTest extends TestCase
{
	/**
	 * Validates the creation of a SubjectId object from a valid string representation.
	 */
	#[Test]
	public function testCreateValidSubjectIdFromString(): void
	{
		$id = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertInstanceOf(SubjectId::class, $id);
		$this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', $id->toNative());
	}

	/**
	 * Ensures that an InvalidSubjectIdException is thrown when attempting to create a SubjectId
	 * object from an invalid string representation.
	 */
	#[Test]
	public function testInvalidSubjectIdThrowsException(): void
	{
		$this->expectException(InvalidSubjectIdException::class);
		SubjectId::fromString('invalid-id');
	}

	/**
	 * Ensures that attempting to create a SubjectId object from a string that is too short
	 * throws an InvalidSubjectIdException.
	 */
	#[Test]
	public function testTooShortSubjectIdThrowsException(): void
	{
		$this->expectException(InvalidSubjectIdException::class);
		SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FA');
	}

	/**
	 * Tests that the equals method returns true when comparing two SubjectId objects created with the same value.
	 */
	#[Test]
	public function testEqualsReturnsTrueForSameValues(): void
	{
		$id1 = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$id2 = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertTrue($id1->equals($id2));
	}

	/**
	 * Verifies that the equals method returns false when comparing two SubjectId objects with different values.
	 */
	#[Test]
	public function testEqualsReturnsFalseForDifferentValues(): void
	{
		$id1 = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$id2 = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FBW');

		$this->assertFalse($id1->equals($id2));
	}

	/**
	 * Verifies that the SubjectId object correctly returns its string representation.
	 */
	#[Test]
	public function testToStringReturnsStringValue(): void
	{
		$id = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertSame('01ARZ3NDEKTSV4RRFFQ69G5FAV', (string) $id);
	}
}
