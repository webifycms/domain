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

namespace Webify\Test\Base\Domain\Collection\Fixture;

/**
 * Item class.
 */
final readonly class Item implements ItemInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private string $value
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function getValue(): string
	{
		return $this->value;
	}
}
