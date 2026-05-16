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

namespace Webify\User\Identity\Domain\Guard;

use Webify\User\Identity\Domain\Exception\EmailMustBeUniqueException;
use Webify\User\Identity\Domain\Repository\UserRepositoryInterface;
use Webify\User\Identity\Domain\ValueObject\UserEmail;

/**
 * Guard prevents against the user registration with existed registered email.
 */
final readonly class EmailMustBeUnique
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private UserRepositoryInterface $repository
	) {}

	/**
	 * Guards against the registration of a user with an email that already exists.
	 *
	 * @throws EmailMustBeUniqueException
	 */
	public function guard(UserEmail $email): void
	{
		if ($this->repository->isExists($email)) {
			throw EmailMustBeUniqueException::create((string) $email);
		}
	}
}
