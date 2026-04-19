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

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Exception\DateTimeException;
use Webify\Base\Domain\ValueObject\DateTime;

/**
 * DateTimeTest tests the functionality of the DateTime value object.
 *
 * @internal
 */
#[CoversClass(DateTime::class)]
#[CoversMethod(DateTime::class, 'now')]
#[CoversMethod(DateTime::class, 'fromNative')]
#[CoversMethod(DateTime::class, 'fromString')]
#[CoversMethod(DateTime::class, 'fromTimestamp')]
#[CoversMethod(DateTime::class, 'toNative')]
#[CoversMethod(DateTime::class, 'toTimezone')]
#[CoversMethod(DateTime::class, 'toFormat')]
#[CoversMethod(DateTime::class, 'defaultFormat')]
#[CoversMethod(DateTime::class, 'equals')]
#[CoversMethod(DateTime::class, 'isBefore')]
#[CoversMethod(DateTime::class, 'isAfter')]
final class DateTimeTest extends TestCase
{
	/**
	 * Test creating a `DateTime` instance using the `now()` factory method.
	 */
	#[Test]
	public function testNowFactoryMethod(): void
	{
		$dateTime = DateTime::now();

		$this->assertInstanceOf(DateTime::class, $dateTime);
		$this->assertInstanceOf(DateTimeImmutable::class, $dateTime->toNative());
		$this->assertSame('UTC', $dateTime->toNative()->getTimezone()->getName());
	}

	/**
	 * Test creating a `DateTime` instance from a native `DateTimeImmutable` object.
	 */
	#[Test]
	public function testFromNativeFactoryMethod(): void
	{
		$nativeDatetime = new DateTimeImmutable('2024-01-15 10:30:00', new DateTimeZone('UTC'));
		$dateTime       = DateTime::fromNative($nativeDatetime);

		$this->assertInstanceOf(DateTime::class, $dateTime);
		$this->assertSame($nativeDatetime, $dateTime->toNative());
	}

	/**
	 * Test creating a `DateTime` instance from a string representation with default timezone.
	 */
	#[Test]
	public function testFromStringWithDefaultTimezone(): void
	{
		$dateString = '2024-01-15 10:30:00';
		$dateTime   = DateTime::fromString($dateString);

		$this->assertInstanceOf(DateTime::class, $dateTime);
		$this->assertSame('UTC', $dateTime->toNative()->getTimezone()->getName());
		$this->assertSame('2024-01-15 10:30:00', $dateTime->defaultFormat());
	}

	/**
	 * Test creating a `DateTime` instance from a string representation with custom timezone.
	 */
	#[Test]
	public function testFromStringWithCustomTimezone(): void
	{
		$dateString = '2024-01-15 10:30:00';
		$timezone   = 'Asia/Colombo';
		$dateTime   = DateTime::fromString($dateString, $timezone);

		$this->assertInstanceOf(DateTime::class, $dateTime);
		$this->assertSame($timezone, $dateTime->toNative()->getTimezone()->getName());
	}

	/**
	 * Test creating a `DateTime` instance from a Unix timestamp.
	 */
	#[Test]
	public function testFromTimestampFactoryMethod(): void
	{
		$timestamp = 1705316400; // 2024-01-15 10:00:00 UTC
		$dateTime  = DateTime::fromTimestamp($timestamp);

		$this->assertInstanceOf(DateTime::class, $dateTime);
		$this->assertSame($timestamp, $dateTime->toNative()->getTimestamp());
	}

	/**
	 * Test converting DateTime to native `DateTimeImmutable`.
	 */
	#[Test]
	public function testToNativeConversion(): void
	{
		$nativeDatetime = new DateTimeImmutable('2024-01-15 10:30:00', new DateTimeZone('UTC'));
		$dateTime       = DateTime::fromNative($nativeDatetime);

		$this->assertSame($nativeDatetime, $dateTime->toNative());
	}

	/**
	 * Test converting `DateTime` to a different timezone.
	 */
	#[Test]
	public function testToTimezoneConversion(): void
	{
		$dateTime          = DateTime::fromString('2024-01-15 10:30:00', 'UTC');
		$convertedDateTime = $dateTime->toTimezone('Asia/Colombo');

		$this->assertInstanceOf(DateTime::class, $convertedDateTime);
		$this->assertSame('Asia/Colombo', $convertedDateTime->toNative()->getTimezone()->getName());
		// Same moment in time (same timestamp)
		$this->assertSame(
			$dateTime->toNative()->getTimestamp(),
			$convertedDateTime->toNative()->getTimestamp()
		);
	}

	/**
	 * Test formatting `DateTime` with default format.
	 */
	#[Test]
	public function testDefaultFormat(): void
	{
		$dateTime  = DateTime::fromString('2024-01-15 10:30:45');
		$formatted = $dateTime->defaultFormat();

		$this->assertSame('2024-01-15 10:30:45', $formatted);
	}

	/**
	 * Test formatting `DateTime` with custom format.
	 */
	#[Test]
	public function testToFormatWithCustomFormat(): void
	{
		$dateTime  = DateTime::fromString('2024-01-15 10:30:45');
		$formatted = $dateTime->toFormat('Y-m-d');

		$this->assertSame('2024-01-15', $formatted);
	}

	/**
	 * Test formatting `DateTime` with custom format and timezone.
	 */
	#[Test]
	public function testToFormatWithCustomFormatAndTimezone(): void
	{
		$format          = 'Y-m-d H:i:s';
		$dateTimeString  = '2024-01-15 10:30:45';
		$phpDateTime     = new DateTimeImmutable($dateTimeString, new DateTimeZone('UTC'));
		$dateTime        = DateTime::fromString($dateTimeString, 'UTC');
		$phpAsiaDateTime = $phpDateTime->setTimezone(new DateTimeZone('Asia/Colombo'));

		$this->assertSame(
			$phpAsiaDateTime->format($format),
			$dateTime->toFormat($format, 'Asia/Colombo')
		);
	}

	/**
	 * Test formatting DateTime with complex format pattern.
	 */
	#[Test]
	public function testToFormatWithComplexPattern(): void
	{
		$dateTime  = DateTime::fromString('2024-01-15 10:30:45');
		$formatted = $dateTime->toFormat('l jS \o\f F Y');

		// Should contain day name and month name
		$this->assertStringContainsString('January', $formatted);
	}

	/**
	 * Test equality between a DateTime object and itself.
	 */
	#[Test]
	public function testEqualsWithSameInstance(): void
	{
		$dateTime = DateTime::fromString('2024-01-15 10:30:00');

		$this->assertTrue($dateTime->equals($dateTime));
	}

	/**
	 * Test equality between two DateTime objects with different values.
	 */
	#[Test]
	public function testEqualsWithDifferentDateTimes(): void
	{
		$dateTime1 = DateTime::fromString('2024-01-15 10:30:00');
		$dateTime2 = DateTime::fromString('2024-01-16 10:30:00');

		$this->assertFalse($dateTime1->equals($dateTime2));
	}

	/**
	 * Test equality between two DateTime objects created from the same native instance.
	 */
	#[Test]
	public function testEqualsWithSameNativeInstance(): void
	{
		$nativeDatetime = new DateTimeImmutable('2024-01-15 10:30:00', new DateTimeZone('UTC'));
		$dateTime1      = DateTime::fromNative($nativeDatetime);
		$dateTime2      = DateTime::fromNative($nativeDatetime);

		$this->assertTrue($dateTime1->equals($dateTime2));
	}

	/**
	 * Test isBefore method with earlier DateTime.
	 */
	#[Test]
	public function testIsBeforeWithEarlierDateTime(): void
	{
		$dateTime1 = DateTime::fromString('2024-01-15 10:30:00');
		$dateTime2 = DateTime::fromString('2024-01-16 10:30:00');

		$this->assertTrue($dateTime1->isBefore($dateTime2));
		$this->assertFalse($dateTime2->isBefore($dateTime1));
	}

	/**
	 * Test isBefore method with equal DateTimes.
	 */
	#[Test]
	public function testIsBeforeWithEqualDateTimes(): void
	{
		$dateTime1 = DateTime::fromString('2024-01-15 10:30:00');
		$dateTime2 = DateTime::fromString('2024-01-15 10:30:00');

		$this->assertFalse($dateTime1->isBefore($dateTime2));
	}

	/**
	 * Test isAfter method with later DateTime.
	 */
	#[Test]
	public function testIsAfterWithLaterDateTime(): void
	{
		$dateTime1 = DateTime::fromString('2024-01-16 10:30:00');
		$dateTime2 = DateTime::fromString('2024-01-15 10:30:00');

		$this->assertTrue($dateTime1->isAfter($dateTime2));
		$this->assertFalse($dateTime2->isAfter($dateTime1));
	}

	/**
	 * Test isAfter method with equal DateTimes.
	 */
	#[Test]
	public function testIsAfterWithEqualDateTimes(): void
	{
		$dateTime1 = DateTime::fromString('2024-01-15 10:30:00');
		$dateTime2 = DateTime::fromString('2024-01-15 10:30:00');

		$this->assertFalse($dateTime1->isAfter($dateTime2));
	}

	/**
	 * Test immutability: toTimezone should return a new instance.
	 */
	#[Test]
	public function testImmutabilityOfToTimezone(): void
	{
		$dateTime          = DateTime::fromString('2024-01-15 10:30:00', 'UTC');
		$convertedDateTime = $dateTime->toTimezone('Asia/Colombo');

		$this->assertNotSame($dateTime, $convertedDateTime);
		$this->assertSame('UTC', $dateTime->toNative()->getTimezone()->getName());
	}

	/**
	 * Test DateTime immutability through readonly property.
	 */
	#[Test]
	public function testDateTimeValueObjectIsImmutable(): void
	{
		$dateTime = DateTime::now();

		$this->assertInstanceOf(DateTimeImmutable::class, $dateTime->toNative());
	}

	/**
	 * Test creating DateTime from Unix timestamp 0 (epoch).
	 */
	#[Test]
	public function testFromTimestampWithEpoch(): void
	{
		$dateTime = DateTime::fromTimestamp(0);

		$this->assertSame(0, $dateTime->toNative()->getTimestamp());
		$this->assertSame('1970-01-01 00:00:00', $dateTime->defaultFormat());
	}

	/**
	 * Test creating DateTime from negative Unix timestamp.
	 */
	#[Test]
	public function testFromTimestampWithNegativeValue(): void
	{
		$timestamp = -86400; // One day before epoch
		$dateTime  = DateTime::fromTimestamp($timestamp);

		$this->assertSame($timestamp, $dateTime->toNative()->getTimestamp());
	}

	/**
	 * Test DateTime comparison chain.
	 */
	#[Test]
	public function testDateTimeComparisonChain(): void
	{
		$dateTime1 = DateTime::fromString('2024-01-15 10:00:00');
		$dateTime2 = DateTime::fromString('2024-01-16 10:00:00');
		$dateTime3 = DateTime::fromString('2024-01-17 10:00:00');

		$this->assertTrue($dateTime1->isBefore($dateTime2));
		$this->assertTrue($dateTime2->isBefore($dateTime3));
		$this->assertTrue($dateTime1->isBefore($dateTime3));

		$this->assertTrue($dateTime3->isAfter($dateTime2));
		$this->assertTrue($dateTime2->isAfter($dateTime1));
		$this->assertTrue($dateTime3->isAfter($dateTime1));
	}

	/**
	 * Test conversion to various timezone formats.
	 */
	#[Test]
	public function testToTimezoneWithVariousTimezones(): void
	{
		$dateTime  = DateTime::fromString('2024-01-15 10:30:00', 'UTC');
		$timezones = ['Asia/Colombo', 'Africa/Harare', 'Europe/Amsterdam', 'Australia/Sydney'];

		foreach ($timezones as $timezone) {
			$converted = $dateTime->toTimezone($timezone);

			$this->assertSame($timezone, $converted->toNative()->getTimezone()->getName());
			// Same moment in time (same timestamp)
			$this->assertSame(
				$dateTime->toNative()->getTimestamp(),
				$converted->toNative()->getTimestamp()
			);
		}
	}

	/**
	 * Test fromString throws DateTimeException for invalid datetime string.
	 */
	#[Test]
	public function testFromStringThrowsExceptionForInvalidDatetime(): void
	{
		$this->expectException(DateTimeException::class);

		DateTime::fromString('invalid-date-string');
	}

	/**
	 * Test fromString throws DateTimeException for invalid timezone.
	 */
	#[Test]
	public function testFromStringThrowsExceptionForInvalidTimezone(): void
	{
		$this->expectException(DateTimeException::class);

		DateTime::fromString('2024-01-15 10:30:00', 'Invalid/Timezone');
	}

	/**
	 * Test toTimezone throws DateTimeException for invalid timezone.
	 */
	#[Test]
	public function testToTimezoneThrowsExceptionForInvalidTimezone(): void
	{
		$this->expectException(DateTimeException::class);

		$dateTime = DateTime::fromString('2024-01-15 10:30:00');
		$dateTime->toTimezone('Invalid/Timezone');
	}

	/**
	 * Test toFormat throws DateTimeException for invalid timezone.
	 */
	#[Test]
	public function testToFormatThrowsExceptionForInvalidTimezone(): void
	{
		$this->expectException(DateTimeException::class);

		$dateTime = DateTime::fromString('2024-01-15 10:30:00');
		$dateTime->toFormat('Y-m-d', 'Invalid/Timezone');
	}

	/**
	 * Test fromString with various invalid datetime strings.
	 */
	#[Test]
	public function testFromStringWithInvalidDatetime1(): void
	{
		$this->expectException(DateTimeException::class);

		DateTime::fromString('not-a-date');
	}

	/**
	 * Test fromString with another invalid datetime string.
	 */
	#[Test]
	public function testFromStringWithInvalidDatetime2(): void
	{
		$this->expectException(DateTimeException::class);

		DateTime::fromString('2024-13-45');
	}
}
