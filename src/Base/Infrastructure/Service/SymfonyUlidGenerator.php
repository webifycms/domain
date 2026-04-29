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

namespace Webify\Base\Infrastructure\Service;

use Symfony\Component\Uid\Ulid;
use Webify\Base\Domain\Service\UlidGeneratorInterface;

/**
 * Implementation of a unique ID generator using Symfony's ULID.
 * ULID stands for Universally Unique Lexicographically Sortable Identifier,
 * which is a 128-bit identifier designed to be unique and sortable based on the time of creation.
 */
final readonly class SymfonyUlidGenerator implements UlidGeneratorInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function generate(): string
	{
		return new Ulid()->toBase32();
	}

	/**
	 * {@inheritDoc}
	 */
	public function generateFrom(string $value): string
	{
		return Ulid::fromString($value)->toBase32();
	}
}
