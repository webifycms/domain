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

use Webify\Base\Domain\Exception\InvalidUlidException;

/**
 * UlidGeneratorInterface defines the contract for generating ULID.
 *
 * This only generates ULIDs that stand for Universally Unique Lexicographically Sortable Identifier,
 * which is a 128-bit identifier designed to be unique and sortable based on the time of creation.
 * It combines a timestamp with a random component, allowing for efficient indexing and retrieval
 * in databases and distributed systems.
 */
interface UlidGeneratorInterface
{
	/**
	 * Generates a new ULID as a string.
	 *
	 * @return string the generated ULID
	 */
	public function generate(): string;

	/**
	 * Normalizes a given value into a ULID string.
	 *
	 * @param string $value the valid ULID string for normalize
	 *
	 * @return string the generated ULID
	 *
	 * @throws InvalidUlidException if the provided value is not a valid ULID
	 */
	public function normalize(string $value): string;
}
