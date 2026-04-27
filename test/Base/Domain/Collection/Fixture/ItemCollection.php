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

use Webify\Base\Domain\Collection\Collection;

/**
 * @template T of Item
 *
 * @extends Collection<T>
 */
final class ItemCollection extends Collection
{
	/**
	 * @return class-string<Item>
	 */
	protected function type(): string
	{
		return Item::class;
	}
}
