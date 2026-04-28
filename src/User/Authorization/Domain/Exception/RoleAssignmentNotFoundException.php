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

use RuntimeException;
use Webify\Base\Domain\Exception\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when the role assignment is not found.
 */
final class RoleAssignmentNotFoundException extends RuntimeException implements TranslatableExceptionInterface
{
	/**
	 * {@inheritDoc}
	 */
	public ExceptionTranslation $translation {
		get {
			return $this->translation;
		}
	}

	/**
	 * The constructor.
	 */
	public function __construct(string $message = 'Role assignment not found.')
	{
		parent::__construct($message);

		$this->translation = new ExceptionTranslation(
			'user.authorization',
			'role_assignment_not_found'
		);
	}
}
