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

namespace Webify\User\Authorization\Application\Query\RoleAssignment;

use Webify\User\Authorization\Domain\ValueObject\SubjectId;

/**
 * Query class defines the contract for a query to find role assignments by subject.
 */
interface FindRoleAssignmentBySubjectInterface
{
	/**
	 * Retrieves a collection of role assignments associated with a specific subject identifier.
	 *
	 * @param SubjectId $subjectId the unique identifier of the subject
	 *
	 * @return RoleAssignmentCollection a collection of role assignment read models linked
	 *                                  to the specified subject
	 */
	public function find(SubjectId $subjectId): RoleAssignmentCollection;
}
