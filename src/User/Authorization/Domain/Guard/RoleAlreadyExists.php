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

namespace Webify\User\Authorization\Domain\Guard;

use Webify\User\Authorization\Domain\Exception\RoleAlreadyExistsException;
use Webify\User\Authorization\Domain\Repository\RoleRepositoryInterface;
use Webify\User\Authorization\Domain\ValueObject\RoleSlug;

/**
 * Guard class prevents against the creation of a role with a slug that already exists.
 */
final readonly class RoleAlreadyExists
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private RoleRepositoryInterface $repository
	) {}

	/**
	 * Guards against the creation of a role with a slug that already exists.
	 *
	 * @param RoleSlug $slug the slug of the role being checked
	 *
	 * @throws RoleAlreadyExistsException if a role with the given slug already exists
	 */
	public function guard(RoleSlug $slug): void
	{
		if ($this->repository->isExist($slug)) {
			throw RoleAlreadyExistsException::forSlug((string) $slug);
		}
	}
}
