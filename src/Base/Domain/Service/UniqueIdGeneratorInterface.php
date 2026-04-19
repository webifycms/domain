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

namespace Webify\Base\Domain\Service;

/**
 * UniqueIdGeneratorInterface defines the contract for generating unique IDs.
 */
interface UniqueIdGeneratorInterface
{
	/**
	 * Generates a new unique ID as string.
	 *
	 * @return string the generated unique ID
	 */
	public function generate(): string;

	/**
	 * Generates a unique ID based on the provided string value.
	 *
	 * @param string $value the input value to generate the unique ID from
	 *
	 * @return string the generated unique ID
	 */
	public function generateFrom(string $value): string;
}
