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

namespace Webify\User\Authorization\Domain\Exception;

use RuntimeException;
use Webify\Base\Domain\Contract\Translation\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when the role is not found.
 */
final class RoleNotFoundException extends RuntimeException implements TranslatableExceptionInterface
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 *
	 * @param ExceptionTranslation $translation the translation object for this exception
	 * @param string               $message     the exception message (optional)
	 */
	private function __construct(
		public readonly ExceptionTranslation $translation,
		string $message = ''
	) {
		parent::__construct($message);
	}

	/**
	 * Creates a new RoleNotFoundException instance for the given role ID.
	 */
	public static function forId(string $id): RoleNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.authorization',
				'role_not_found',
				['id'=> $id]
			),
			sprintf('Role not found for id: "%s"', $id)
		);
	}

	/**
	 * Creates a new RoleNotFoundException instance for the given role slug.
	 */
	public static function forSlug(string $slug): RoleNotFoundException
	{
		return new self(
			new ExceptionTranslation(
				'user.authorization',
				'role_not_found',
				['slug'=> $slug]
			),
			sprintf('Role not found for slug: "%s"', $slug)
		);
	}
}
