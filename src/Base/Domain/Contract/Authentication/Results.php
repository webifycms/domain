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

namespace Webify\Base\Domain\Contract\Authentication;

use Webify\Base\Contracts\KeyValueReaderInterface;

/**
 * Results hold the information about the authentication.
 */
final readonly class Results implements KeyValueReaderInterface
{
	/**
	 * The constructor.
	 *
	 * @param bool                 $authenticated whether the user is authenticated
	 * @param array<string, mixed> $data          the authentication result data
	 */
	public function __construct(
		private bool $authenticated,
		private array $data
	) {}

	/**
	 * Checks if the user is authenticated.
	 */
	public function isAuthenticated(): bool
	{
		return $this->authenticated;
	}

	/**
	 * Returns the authentication result.
	 *
	 * @return array<string, mixed>
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * {@inheritDoc}
	 */
	public function has(int|string $key): bool
	{
		return array_key_exists($key, $this->data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(int|string $key, mixed $default = null): mixed
	{
		return $this->data[$key] ?? $default;
	}
}
