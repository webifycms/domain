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

namespace Webify\User\Authorization\Domain\Exception;

use DomainException;
use Webify\Base\Domain\Exception\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Exception thrown when a role assignment already exists.
 */
final class DuplicateRoleAssignmentException extends DomainException implements TranslatableExceptionInterface
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this exception.
	 *
	 * @param ExceptionTranslation $translation the translation object for this exception
	 * @param string               $message     the exception message (optional)
	 */
	private function __construct(
		public readonly ExceptionTranslation $translation,
		string $message = ''
	) {
		parent::__construct($message);
	}

	/**
	 * Factory method to initiate this with a default message.
	 */
	public static function forRoleAndSubject(string $roleId, string $subjetId): self
	{
		return new self(
			new ExceptionTranslation(
				'user.authorization',
				'role_assignment_exists',
				[
					'roleId'    => $roleId,
					'subjectId' => $subjetId,
				]
			),
			sprintf(
				'Role assignment is already exists for the given role "%s" and subject "%s".',
				$roleId,
				$subjetId
			)
		);
	}
}
