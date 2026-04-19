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

/**
 * A simple data transfer object to hold translation information for exceptions.
 */
final readonly class ExceptionTranslation
{
	/**
	 * ExceptionTranslation constructor.
	 *
	 * @param string               $group  the translation group or domain
	 * @param string               $key    the translation key
	 * @param array<string, mixed> $params an associative array of parameters for the translation message
	 */
	public function __construct(
		public string $group,
		public string $key,
		public array $params
	) {}
}
