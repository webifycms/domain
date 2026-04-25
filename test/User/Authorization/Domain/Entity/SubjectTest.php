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

namespace Webify\Test\User\Authorization\Domain\Entity;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authorization\Domain\Entity\Subject;
use Webify\User\Authorization\Domain\ValueObject\{SubjectAttributes, SubjectId, SubjectType, TenantId};

/**
 * SubjectTest tests the functionality of the Subject entity.
 *
 * @internal
 */
#[CoversClass(Subject::class)]
#[CoversMethod(Subject::class, 'create')]
#[CoversMethod(Subject::class, 'getId')]
#[CoversMethod(Subject::class, 'getType')]
#[CoversMethod(Subject::class, 'getAttributes')]
#[CoversMethod(Subject::class, 'getTenantId')]
#[CoversMethod(Subject::class, 'hasAttribute')]
#[CoversMethod(Subject::class, 'getAttribute')]
final class SubjectTest extends TestCase
{
	/**
	 * Tests the creation of a Subject instance and verifies its properties.
	 */
	#[Test]
	public function testCreateSubject(): void
	{
		$subject = Subject::create(
			SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			new SubjectType('user'),
			SubjectAttributes::fromArray(['department' => 'management'])
		);

		$this->assertInstanceOf(Subject::class, $subject);
		$this->assertInstanceOf(SubjectId::class, $subject->getId());
		$this->assertInstanceOf(SubjectType::class, $subject->getType());
		$this->assertInstanceOf(SubjectAttributes::class, $subject->getAttributes());
		$this->assertNull($subject->getTenantId());
	}

	/**
	 * Tests the creation of a Subject instance with an associated TenantId and verifies its properties.
	 */
	#[Test]
	public function testCreateSubjectWithTenantId(): void
	{
		$subject = Subject::create(
			SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			new SubjectType('user'),
			SubjectAttributes::fromArray(['department' => 'management']),
			TenantId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV')
		);

		$this->assertInstanceOf(TenantId::class, $subject->getTenantId());
	}

	/**
	 * Tests whether the method hasAttribute returns true when the specified attribute exists.
	 */
	#[Test]
	public function testHasAttributeReturnsTrue(): void
	{
		$subject = Subject::create(
			SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			new SubjectType('user'),
			SubjectAttributes::fromArray(['department' => 'management'])
		);

		$this->assertTrue($subject->hasAttribute('department'));
	}

	/**
	 * Tests whether the method hasAttribute returns false when the specified attribute does not exist.
	 */
	#[Test]
	public function testHasAttributeReturnsFalse(): void
	{
		$subject = Subject::create(
			SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			new SubjectType('user'),
			SubjectAttributes::fromArray(['department' => 'management'])
		);

		$this->assertFalse($subject->hasAttribute('email'));
	}

	/**
	 * Tests that the getAttribute method returns the expected attribute from the Subject instance.
	 */
	#[Test]
	public function testGetAttributeReturnsAttribute(): void
	{
		$subject = Subject::create(
			SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			new SubjectType('user'),
			SubjectAttributes::fromArray(['department' => 'management'])
		);

		$result = $subject->getAttribute('department');

		$this->assertSame(['department' => 'management'], $result);
	}

	/**
	 * Tests that the getAttribute method returns an empty array when the requested attribute
	 * does not exist in the Subject instance.
	 */
	#[Test]
	public function testGetAttributeReturnsEmptyForNonExistent(): void
	{
		$subject = Subject::create(
			SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV'),
			new SubjectType('user'),
			SubjectAttributes::fromArray(['department' => 'management'])
		);

		$result = $subject->getAttribute('email');

		$this->assertSame([], $result);
	}
}
