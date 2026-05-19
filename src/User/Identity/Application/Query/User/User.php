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

/**
 * User read model.
 */
final readonly class User
{
	/**
	 * Constructor method to initialize the object with the provided parameters.
	 *
	 * @param string $id          unique identifier for the object
	 * @param string $email       email address associated with the object
	 * @param string $displayName display name for the object
	 * @param string $status      current status of the object
	 * @param string $createdAt   creation timestamp of the object
	 * @param string $updatedAt   last updated timestamp of the object
	 */
	public function __construct(
		public string $id,
		public string $email,
		public string $displayName,
		public string $status,
		public string $createdAt,
		public string $updatedAt
	) {}
}
