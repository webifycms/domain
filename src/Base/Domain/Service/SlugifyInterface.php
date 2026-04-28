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
 * SlugifyInterface defines the contract for converting strings into URL-friendly slugs.
 */
interface SlugifyInterface
{
	/**
	 * Converts a given string into a URL-friendly slug by transforming it to lowercase,
	 * removing special characters, and replacing spaces with hyphens.
	 *
	 * @param string $text the input string to be converted into a slug
	 *
	 * @return string the slugified version of the input string
	 */
	public function slugify(string $text): string;
}
