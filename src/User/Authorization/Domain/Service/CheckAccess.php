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

namespace Webify\User\Authorization\Domain\Service;

use Webify\Base\Domain\Contract\Authorization\{AuthorizableResourceInterface, AuthorizableSubjectInterface};
use Webify\Base\Domain\Exception\AccessDeniedException;
use Webify\Base\Domain\Service\{Webify\Base\Domain\Service\Authorization\AuthorizationInterface,
	Webify\Base\Domain\Service\Authorization\CheckAccessInterface};

/**
 * CheckAccess is the implementation of the `CheckAccessInterface` that uses the
 * `AuthorizationInterface` to check access permissions.
 */
final readonly class CheckAccess implements \Webify\Base\Domain\Service\Authorization\CheckAccessInterface
{
	/**
	 * The constructor.
	 */
	public function __construct(
		private \Webify\Base\Domain\Service\Authorization\AuthorizationInterface $authorization
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function isAllowed(
		string $action,
		AuthorizableSubjectInterface $subject,
		AuthorizableResourceInterface $resource
	): bool {
		return $this->authorization->check($action, $subject, $resource);
	}

	/**
	 * {@inheritDoc}
	 */
	public function denyUnless(
		string $action,
		AuthorizableSubjectInterface $subject,
		AuthorizableResourceInterface $resource
	): void {
		if (!$this->authorization->check($action, $subject, $resource)) {
			throw AccessDeniedException::for($action, $subject->subjectId(), $resource->resourceId());
		}
	}
}
