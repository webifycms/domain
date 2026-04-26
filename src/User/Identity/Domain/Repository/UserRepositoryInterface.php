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

namespace Webify\User\Identity\Domain\Repository;

use Webify\User\Identity\Domain\Entity\User;
use Webify\User\Identity\Domain\Exception\UserNotFoundException;
use Webify\User\Identity\Domain\ValueObject\UserId;

/**
 * UserRepositoryInterface defines the contract for a user repository.
 */
interface UserRepositoryInterface
{
	/**
	 * Retrieves an entity based on the provided unique UserId.
	 *
	 * @param UserId $id the unique identifier of the user to retrieve
	 *
	 * @throws UserNotFoundException if the user is not found
	 */
	public function getById(UserId $id): User;

	/**
	 * Persists the given user entity to the underlying storage.
	 *
	 * @param User $user the user entity to be persisted
	 */
	public function persist(User $user): void;
}
