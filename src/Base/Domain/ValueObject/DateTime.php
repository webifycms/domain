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

namespace Webify\Base\Domain\ValueObject;

use DateInvalidTimeZoneException;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;
use Webify\Base\Domain\Exception\DateTimeException;

/**
 * The DateTime value object class.
 */
final readonly class DateTime
{
	/**
	 * Default timezone.
	 */
	private const string DEFAULT_TIMEZONE = 'UTC';

	/**
	 * Default format.
	 */
	private const string DEFAULT_FORMAT = 'Y-m-d H:i:s';

	/**
	 * The private constructor enforces to use factory methods to initiate this object.
	 */
	private function __construct(
		private DateTimeImmutable $value
	) {}

	/**
	 * A factory method to create a new DateTime object with the current date and time.
	 *
	 * @throws DateTimeException if unable to create DateTime object with the current datetime and default timezone
	 */
	public static function now(): static
	{
		try {
			return new self(
				new DateTimeImmutable(
					'now',
					new DateTimeZone(self::DEFAULT_TIMEZONE)
				)
			);
		} catch (DateInvalidTimeZoneException|DateMalformedStringException $throwable) {
			throw DateTimeException::fromDefault(previous: $throwable);
		}
	}

	/**
	 * A factory method to create a new DateTime object from a native DateTimeImmutable instance.
	 */
	public static function fromNative(DateTimeImmutable $value): static
	{
		return new self($value);
	}

	/**
	 * A factory method to create a new DateTime object from a string representation of date and time.
	 *
	 * @param string      $datetime The date and time string (e.g., '2024-01-01 12:00:00').
	 * @param null|string $timezone Optional timezone (e.g., 'Asia/Colombo'). Defaults to UTC if not provided.
	 *
	 * @return static a new instance of DateTime
	 *
	 * @throws DateTimeException if the provided date and time string is invalid or if the timezone is invalid
	 */
	public static function fromString(string $datetime, ?string $timezone = null): static
	{
		try {
			$timezone ??= self::DEFAULT_TIMEZONE;

			return new self(
				new DateTimeImmutable(
					$datetime,
					new DateTimeZone($timezone)
				)
			);
		} catch (DateInvalidTimeZoneException $throwable) {
			$exception = DateTimeException::fromInvalidTimezone(value: $timezone, previous: $throwable);
		} catch (DateMalformedStringException $throwable) {
			$exception = DateTimeException::fromInvalidDatetime(value: $datetime, previous: $throwable);
		}

		throw $exception;
	}

	/**
	 * A factory method to create a new DateTime object from a Unix timestamp.
	 *
	 * @param int $timestamp the Unix timestamp (number of seconds since January 1, 1970)
	 *
	 * @return static a new instance of DateTime
	 */
	public static function fromTimestamp(int $timestamp): static
	{
		return new self(
			new DateTimeImmutable()->setTimestamp($timestamp)
		);
	}

	/**
	 * Get the native DateTimeImmutable value.
	 *
	 * @return DateTimeImmutable the native DateTimeImmutable instance
	 */
	public function toNative(): DateTimeImmutable
	{
		return $this->value;
	}

	/**
	 * Converts the DateTime to a different timezone.
	 * Will re-normalize to the default timezone internally
	 * and returns a new instance (immutability preserved) in the given timezone.
	 *
	 * @param string $timezone the target timezone (e.g., 'Asia/Colombo')
	 *
	 * @return self a new DateTime instance with the converted timezone
	 */
	public function toTimezone(string $timezone): self
	{
		try {
			return new self(
				$this->value->setTimezone(new DateTimeZone($timezone))
			);
		} catch (DateInvalidTimeZoneException $throwable) {
			throw DateTimeException::fromInvalidTimezone(value: $timezone, previous: $throwable);
		}
	}

	/**
	 * Format the date and time using a specified format string.
	 *
	 * @param string $format The format string (e.g., 'l jS \o\f F Y h:i:s A').
	 *
	 * @return string the formatted date and time string (e.g., 'Wednesday 19th of October 2022 08:40:48 AM')
	 */
	public function toFormat(string $format, ?string $timezone = null): string
	{
		try {
			$value = $timezone
				? $this->value->setTimezone(new DateTimeZone($timezone))
				: $this->value;

			return $value->format($format);
		} catch (DateInvalidTimeZoneException $throwable) {
			throw DateTimeException::fromInvalidTimezone(value: $timezone, previous: $throwable);
		}
	}

	/**
	 * Format the date and time using the default format.
	 *
	 * @return string the formatted date and time string in the default format `Y-m-d H:i:s`
	 */
	public function defaultFormat(): string
	{
		return $this->value->format(self::DEFAULT_FORMAT);
	}

	/**
	 * Compare this DateTime object with another for equality.
	 *
	 * @param DateTime $other the other DateTime object to compare with
	 *
	 * @return bool true if both DateTime objects represent the same date and time, false otherwise
	 */
	public function equals(DateTime $other): bool
	{
		return $this->toNative() === $other->toNative();
	}

	/**
	 * Check if this DateTime is before another DateTime.
	 *
	 * @param DateTime $other the other DateTime object to compare with
	 *
	 * @return bool true if this DateTime is before the other, false otherwise
	 */
	public function isBefore(DateTime $other): bool
	{
		return $this->toNative() < $other->toNative();
	}

	/**
	 * Check if this DateTime is after another DateTime.
	 *
	 * @param DateTime $other the other DateTime object to compare with
	 *
	 * @return bool true if this DateTime is after the other, false otherwise
	 */
	public function isAfter(DateTime $other): bool
	{
		return $this->toNative() > $other->toNative();
	}
}
