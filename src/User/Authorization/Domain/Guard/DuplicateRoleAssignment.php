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

use Webify\User\Authorization\Domain\Exception\DuplicateRoleAssignmentException;
use Webify\User\Authorization\Domain\Repository\RoleAssignmentRepositoryInterface;
use Webify\User\Authorization\Domain\ValueObject\{RoleId, SubjectId};

/**
 * Guard class to prevent duplicate role assignments.
 */
final readonly class DuplicateRoleAssignment
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private RoleAssignmentRepositoryInterface $repository
	) {}

	/**
	 * Ensures that a role-subject assignment does not already exist.
	 *
	 * @param RoleId    $roleId    the identifier for the role
	 * @param SubjectId $subjectId the identifier for the subject
	 *
	 * @throws DuplicateRoleAssignmentException if the role-subject assignment already exists
	 */
	public function guard(RoleId $roleId, SubjectId $subjectId): void
	{
		if ($this->repository->isExists($roleId, $subjectId)) {
			throw DuplicateRoleAssignmentException::forRoleAndSubject($roleId->toNative(), $subjectId->toNative());
		}
	}
}
