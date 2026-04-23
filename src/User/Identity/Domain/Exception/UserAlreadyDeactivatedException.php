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

namespace Webify\User\Identity\Domain\Exception;

use LogicException;
use Webify\Base\Domain\Exception\{ExceptionTranslation, TranslatableExceptionInterface};

/**
 * Thrown when a user was already deactivated and trying to deactivate again.
 */
final class UserAlreadyDeactivatedException extends LogicException implements TranslatableExceptionInterface
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
	public function __construct(string $message = 'User is already deactivated')
	{
		parent::__construct($message);

		$this->translation = new ExceptionTranslation(
			'user.identity',
			'user_already_deactivated'
		);
	}
}
