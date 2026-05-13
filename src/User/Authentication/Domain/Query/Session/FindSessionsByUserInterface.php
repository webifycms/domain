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

namespace Webify\User\Authentication\Domain\Query\Session;

use Webify\User\Authentication\Domain\ValueObject\UserId;

/**
 * Query class defines the contract for a query to find sessions by user.
 */
interface FindSessionsByUserInterface
{
	/**
	 * Finds all sessions by user.
	 *
	 * @param UserId $userId the identifier of the user whose sessions are to be found
	 *
	 * @return SessionCollection a collection of sessions associated with the given user identifier,
	 *                           or an empty session collection if no sessions exist
	 */
	public function find(UserId $userId): SessionCollection;

	/**
	 * Finds all active sessions for the user.
	 *
	 * @param UserId $userId the identifier of the user whose active sessions are to be found
	 *
	 * @return SessionCollection a collection of active sessions associated with the given user identifier,
	 *                           or an empty session collection if no active sessions exist
	 */
	public function findActive(UserId $userId): SessionCollection;
}
