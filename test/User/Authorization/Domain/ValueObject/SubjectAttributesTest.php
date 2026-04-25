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
use Webify\User\Authorization\Domain\Exception\InvalidSubjectAttributesException;
use Webify\User\Authorization\Domain\ValueObject\SubjectAttributes;

/**
 * SubjectAttributesTest tests the functionality of the SubjectAttributes value object.
 *
 * @internal
 */
#[CoversClass(SubjectAttributes::class)]
#[CoversMethod(SubjectAttributes::class, 'fromArray')]
#[CoversMethod(SubjectAttributes::class, 'toNative')]
#[CoversMethod(SubjectAttributes::class, 'get')]
final class SubjectAttributesTest extends TestCase
{
	/**
	 * Tests the creation of an empty SubjectAttributes instance.
	 */
	#[Test]
	public function testCreateEmptyAttributes(): void
	{
		$attributes = SubjectAttributes::fromArray([]);

		$this->assertInstanceOf(SubjectAttributes::class, $attributes);
		$this->assertSame([], $attributes->toNative());
	}

	/**
	 * Tests the creation of valid attributes using the SubjectAttributes class.
	 */
	#[Test]
	public function testCreateValidAttributes(): void
	{
		$attributes = SubjectAttributes::fromArray([
			'department' => 'management',
			'level'      => 'top',
		]);

		$this->assertInstanceOf(SubjectAttributes::class, $attributes);
		$this->assertSame(
			[
				'department' => 'management',
				'level'      => 'top',
			],
			$attributes->toNative()
		);
	}

	/**
	 * Tests that providing invalid attributes to the SubjectAttributes::fromArray method
	 * results in an InvalidSubjectAttributesException being thrown.
	 */
	#[Test]
	public function testInvalidAttributesThrowsException(): void
	{
		$this->expectException(InvalidSubjectAttributesException::class);
		// @phpstan-ignore-next-line
		SubjectAttributes::fromArray(['invalid', 'list']);
	}

	/**
	 * Tests that the get method of SubjectAttributes correctly retrieves an existing attribute
	 * by its key and returns the expected value.
	 */
	#[Test]
	public function testGetExistingAttribute(): void
	{
		$attributes = SubjectAttributes::fromArray([
			'department' => 'management',
			'level'      => 'top',
		]);
		$result = $attributes->get('department');

		$this->assertSame(['department' => 'management'], $result);
	}

	/**
	 * Tests that attempting to retrieve a non-existing attribute using the SubjectAttributes::get method
	 * returns an empty array.
	 */
	#[Test]
	public function testGetNonExistingAttribute(): void
	{
		$attributes = SubjectAttributes::fromArray([
			'department' => 'management',
		]);
		$result = $attributes->get('email');

		$this->assertSame([], $result);
	}

	/**
	 * Tests that the toNative method of SubjectAttributes correctly returns the array representation
	 * of the attributes provided during instantiation.
	 */
	#[Test]
	public function testToNativeReturnsArrayValue(): void
	{
		$attributes = SubjectAttributes::fromArray([
			'department' => 'management',
			'level'      => 'top',
		]);

		$this->assertSame(
			[
				'department' => 'management',
				'level'      => 'top',
			],
			$attributes->toNative()
		);
	}
}
