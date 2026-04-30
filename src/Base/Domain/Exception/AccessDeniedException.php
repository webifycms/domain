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

namespace Webify\Base\Domain\Exception;

use DomainException;

/**
 * Exception thrown when an access denied error occurs in the authorization domain.
 */
final class AccessDeniedException extends DomainException implements TranslatableExceptionInterface
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
	 *
	 * @param string $action     the action being performed
	 * @param string $subjectId  the ID of the subject
	 * @param string $resourceId the ID of the resource
	 */
	public static function for(string $action, string $subjectId, string $resourceId): self
	{
		return new self(
			new ExceptionTranslation(
				'user.authorization',
				'access_denied',
				[
					'action'     => $action,
					'subjectId'  => $subjectId,
					'resourceId' => $resourceId,
				]
			),
			sprintf(
				'Access denied for action "%s" on subject "%s" and resource "%s".',
				$action,
				$subjectId,
				$resourceId
			)
		);
	}
}
