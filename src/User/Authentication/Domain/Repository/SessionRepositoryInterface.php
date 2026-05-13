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

namespace Webify\User\Authentication\Domain\Repository;

use Webify\User\Authentication\Domain\Entity\Session;
use Webify\User\Authentication\Domain\Exception\SessionNotFoundException;
use Webify\User\Authentication\Domain\ValueObject\{AccessToken, RefreshToken, SessionId};

/**
 * SessionRepositoryInterface defines the contract for the session repository.
 */
interface SessionRepositoryInterface
{
	/**
	 * Get a session by its ID.
	 *
	 * @throws SessionNotFoundException if the session with the given ID is not found
	 */
	public function getById(SessionId $id): Session;

	/**
	 * Get a session by access token.
	 *
	 * @throws SessionNotFoundException if the session with the given access token is not found
	 */
	public function getByAccessToken(AccessToken $accessToken): Session;

	/**
	 * Get a session by refresh token.
	 *
	 * @throws SessionNotFoundException if the session with the given refresh token is not found
	 */
	public function getByRefreshToken(RefreshToken $refreshToken): Session;

	/**
	 * Persists the given Session object to the data store.
	 */
	public function persist(Session $session): void;

	/**
	 * Deletes the specified Session.
	 */
	public function delete(Session $session): void;
}
