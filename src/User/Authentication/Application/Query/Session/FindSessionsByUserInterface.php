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

namespace Webify\User\Authentication\Application\Query\Session;

use Webify\User\Authentication\Domain\ValueObject\ChallengeSecretInterface;

/**
 * Query class defines the contract for a query to find sessions by user.
 */
interface FindSessionsByUserInterface
{
	/**
	 * Finds all sessions by user.
	 *
	 * @param ChallengeSecretInterface $userId the identifier of the user whose sessions are to be found
	 *
	 * @return SessionCollection a collection of sessions associated with the given user identifier,
	 *                           or an empty session collection if no sessions exist
	 */
	public function find(ChallengeSecretInterface $userId): SessionCollection;

	/**
	 * Finds all active sessions for the user.
	 *
	 * @param ChallengeSecretInterface $userId the identifier of the user whose active sessions are to be found
	 *
	 * @return SessionCollection a collection of active sessions associated with the given user identifier,
	 *                           or an empty session collection if no active sessions exist
	 */
	public function findActive(ChallengeSecretInterface $userId): SessionCollection;
}
