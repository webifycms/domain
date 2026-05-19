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

namespace Webify\User\Identity\Application\Query\User;

use Webify\User\Identity\Domain\ValueObject\UserEmail;

/**
 * Query class defines the contract for a query to find a user by its identifier.
 */
interface FindUserByEmailInterface
{
	/**
	 * Finds a User by its email.
	 *
	 * @param UserEmail $email the email of the user to find
	 *
	 * @return null|User the User read-model if found, or null if no user matches the given email
	 */
	public function find(UserEmail $email): ?User;
}
