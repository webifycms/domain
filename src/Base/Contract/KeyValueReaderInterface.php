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

namespace Webify\Base\Contract;

/**
 * The KeyValueReaderInterface defines the contract for reading data from a key-value store.
 */
interface KeyValueReaderInterface
{
	/**
	 * Checks the data exist for the given key.
	 *
	 * @param int|string $key the key to check for
	 */
	public function has(int|string $key): bool;

	/**
	 * Returns the data or default for the given key.
	 *
	 * @param int|string $key     the key to retrieve the data for
	 * @param null|mixed $default the default value to return if the key is not found
	 */
	public function get(int|string $key, mixed $default = null): mixed;
}
