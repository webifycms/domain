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

namespace Webify\Base\Domain\Exception;

use InvalidArgumentException;
use Throwable;

/**
 * Exception thrown when an invalid date and time value is encountered.
 */
final class DateTimeException extends InvalidArgumentException implements TranslatableExceptionInterface
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 *
	 * @param ExceptionTranslation $translation the translation object for this exception
	 * @param string               $message     the exception message (optional)
	 * @param int                  $code        the exception code (optional)
	 * @param null|Throwable       $previous    the previous throwable used for the exception chaining (optional)
	 */
	private function __construct(
		private readonly ExceptionTranslation $translation,
		string $message = '',
		int $code = 0,
		?Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}

	/**
	 * Factory method to create a `DateTimeException` for a default error scenario.
	 *
	 * @param int            $code     the exception code (optional)
	 * @param null|Throwable $previous the previous throwable used for the exception chaining (optional
	 *
	 * @return static a new instance of `DateTimeException`
	 */
	public static function fromDefault(int $code = 0, ?Throwable $previous = null): static
	{
		return new self(
			new ExceptionTranslation(
				'base.datetime',
				'default',
				[]
			),
			'Failed to create DateTime object with current date and time.',
			$code,
			$previous
		);
	}

	/**
	 * Factory method to create a DateTimeException for a malformed string.
	 *
	 * @param string         $value    the invalid date and time string
	 * @param int            $code     the exception code (optional)
	 * @param null|Throwable $previous the previous throwable used for the exception chaining (optional)
	 *
	 * @return static a new instance of DateTimeException
	 */
	public static function fromInvalidDatetime(string $value, int $code = 0, ?Throwable $previous = null): static
	{
		return new self(
			new ExceptionTranslation(
				'base.datetime',
				'invalid_datetime',
				['value' => $value]
			),
			sprintf(
				'Failed to create DateTime object due to invalid datetime "%s"',
				$value
			),
			$code,
			$previous
		);
	}

	/**
	 * Factory method to create a DateTimeException for a malformed string.
	 *
	 * @param string         $value    the invalid date and time string
	 * @param int            $code     the exception code (optional)
	 * @param null|Throwable $previous the previous throwable used for the exception chaining (optional)
	 *
	 * @return static a new instance of DateTimeException
	 */
	public static function fromInvalidTimezone(string $value, int $code = 0, ?Throwable $previous = null): static
	{
		return new self(
			new ExceptionTranslation(
				'base.datetime',
				'invalid_timezone',
				['value' => $value]
			),
			sprintf(
				'Failed to create DateTime object due to invalid timezone "%s"',
				$value
			),
			$code,
			$previous
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTranslation(): ExceptionTranslation
	{
		return $this->translation;
	}
}
