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

namespace Webify\Base\Domain\Contract\Authorization;

/**
 * The contract for authorizable subjects.
 */
interface AuthorizableSubjectInterface
{
	/**
	 * Retrieves the subject identifier.
	 */
	public function subjectId(): string;

	/**
	 * Retrieves the attributes associated with the subject.
	 *
	 * @return null|array<string, string> an associative array of attributes, or null if no attributes are available
	 */
	public function attributes(): ?array;

	/**
	 * Retrieves the tenant identifier.
	 *
	 * @return null|string the tenant identifier or null if not available
	 */
	public function tenantId(): ?string;
}
