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

namespace Webify\Base\Domain\Contract\Authentication;

use Webify\Base\Contracts\KeyValueReaderInterface;

/**
 * RequestInterface defines the contract for authentication request credentials.
 */
interface Request extends KeyValueReaderInterface
{
	/**
	 * Returns the identifier of the authentication strategy.
	 * This identifier is used to identify the strategy from the registry.
	 */
	public function getStrategyIdentifier(): string;

	/**
	 * Returns the payload of the authentication request.
	 * The payload contains the credentials required by the authentication strategy.
	 *
	 * 1. In the case of an email/password authentication, the payload should contain the email and password.
	 * 2. In the case of a 2FA authentication, the payload should contain the code or token.
	 *
	 * @return array<string, mixed>
	 */
	public function getPayload(): array;
}
