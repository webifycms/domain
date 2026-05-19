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

use Webify\User\Authentication\Domain\ValueObject\SessionId;

/**
 * Query class defines the contract for a query to find a session by its identifier.
 */
interface FindSessionByIdInterface
{
	/**
	 * Finds a session by its identifier.
	 *
	 * @param SessionId $id the identifier of the session to find
	 *
	 * @return null|Session the session if found, or null if no session matches the given identifier
	 */
	public function find(SessionId $id): ?Session;
}
