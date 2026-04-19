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
use Webify\Base\Domain\ValueObject\AggregateId;

/**
 * AggregateIdTest tests the functionality of the AggregateId class.
 *
 * @internal
 */
#[CoversClass(AggregateId::class)]
#[CoversMethod(AggregateId::class, '__construct')]
#[CoversMethod(AggregateId::class, '__toString')]
#[CoversMethod(AggregateId::class, 'toNative')]
#[CoversMethod(AggregateId::class, 'equals')]
#[CoversMethod(AggregateId::class, 'fromString')]
final class AggregateIdTest extends TestCase
{
	/**
	 * Test creating a valid aggregate ID.
	 */
	#[Test]
	public function testCreateValidAggregateId(): void
	{
		$aggregateId = $this->createConcreteAggregateId('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertInstanceOf(AggregateId::class, $aggregateId);
	}

	/**
	 * Test invalid aggregate ID throws exception.
	 */
	#[Test]
	public function testInvalidAggregateIdThrowsException(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->createConcreteAggregateId('invalid-id-format');
	}

	/**
	 * Test aggregate ID with invalid first character (must be 0-7).
	 */
	#[Test]
	public function testInvalidFirstCharacterThrowsException(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->createConcreteAggregateId('81ARZ3NDEKTSV4RRFFQ69G5FAV');
	}

	/**
	 * Test to string conversion.
	 */
	#[Test]
	public function testToStringConversion(): void
	{
		$value       = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$aggregateId = $this->createConcreteAggregateId($value);

		$this->assertSame($value, (string) $aggregateId);
	}

	/**
	 * Test to native method.
	 */
	#[Test]
	public function testToNativeMethod(): void
	{
		$value       = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$aggregateId = $this->createConcreteAggregateId($value);

		$this->assertSame($value, $aggregateId->toNative());
	}

	/**
	 * Test equals method with equal IDs.
	 */
	#[Test]
	public function testEqualsWithEqualIds(): void
	{
		$value        = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$aggregateId1 = $this->createConcreteAggregateId($value);
		$aggregateId2 = $this->createConcreteAggregateId($value);

		$this->assertTrue($aggregateId1->equals($aggregateId2));
	}

	/**
	 * Test equals method with different IDs.
	 */
	#[Test]
	public function testEqualsWithDifferentIds(): void
	{
		$aggregateId1 = $this->createConcreteAggregateId('01ARZ3NDEKTSV4RRFFQ69G5FAV');
		$aggregateId2 = $this->createConcreteAggregateId('01ARZ3NDEKTSV4RRFFQ69G5FAW');

		$this->assertFalse($aggregateId1->equals($aggregateId2));
	}

	/**
	 * Test from string factory method.
	 */
	#[Test]
	public function testFromStringFactoryMethod(): void
	{
		$value       = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$aggregateId = $this->createConcreteAggregateIdFromString($value);

		$this->assertSame($value, $aggregateId->toNative());
	}

	/**
	 * Test from string converts to uppercase.
	 */
	#[Test]
	public function testFromStringConvertsToUppercase(): void
	{
		$lowercaseValue = '01arz3ndektsv4rrffq69g5fav';
		$expectedValue  = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

		$aggregateId = $this->createConcreteAggregateIdFromString($lowercaseValue);

		$this->assertSame($expectedValue, $aggregateId->toNative());
	}

	/**
	 * Test from string with mixed case converts to uppercase.
	 */
	#[Test]
	public function testFromStringWithMixedCaseConvertsToUppercase(): void
	{
		$mixedCaseValue = '01ArZ3nDeKtSv4RrFfQ69g5FaV';
		$expectedValue  = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

		$aggregateId = $this->createConcreteAggregateIdFromString($mixedCaseValue);

		$this->assertSame($expectedValue, $aggregateId->toNative());
	}

	/**
	 * Create a concrete implementation of AggregateId for testing.
	 */
	private function createConcreteAggregateId(string $value): AggregateId
	{
		return new ExampleAggregateId($value);
	}

	/**
	 * Create a concrete implementation of AggregateId using fromString factory method.
	 */
	private function createConcreteAggregateIdFromString(string $value): AggregateId
	{
		return ExampleAggregateId::fromString($value);
	}
}
