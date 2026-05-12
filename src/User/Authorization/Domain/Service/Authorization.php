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
use Webify\Base\Domain\Service\Authorization\{AuthorizationInterface, AuthorizationRuleRegistryInterface};
use Webify\User\Authorization\Domain\Exception\RoleNotFoundException;
use Webify\User\Authorization\Domain\Repository\{RoleAssignmentRepositoryInterface, RoleRepositoryInterface};
use Webify\User\Authorization\Domain\ValueObject\{SubjectId, TenantId};

/**
 * Authorization class provides the implementation of the AuthorizationInterface.
 */
final class Authorization implements AuthorizationInterface
{
	/**
	 * Cache for authorization results.
	 * Key: Cache key (action, subjectId, resourceScope, resourceType, tenantId, or global).
	 * Value: Authorization result (true or false).
	 *
	 * @var array<string, bool>
	 */
	private array $cache = [];

	/**
	 * The constructor.
	 */
	public function __construct(
		private readonly RoleRepositoryInterface $roleRepository,
		private readonly RoleAssignmentRepositoryInterface $assignmentRepository,
		private readonly AuthorizationRuleRegistryInterface $ruleRegistry
	) {}

	/**
	 * {@inheritDoc}
	 */
	public function check(
		string $action,
		AuthorizableSubjectInterface $subject,
		AuthorizableResourceInterface $resource
	): bool {
		$key = $this->generateCacheKey($action, $subject, $resource);

		if (isset($this->cache[$key])) {
			return $this->cache[$key];
		}

		return $this->cache[$key] = $this->evaluate($action, $subject, $resource);
	}

	/**
	 * Evaluates the authorization based on the provided parameters.
	 */
	private function evaluate(
		string $action,
		AuthorizableSubjectInterface $subject,
		AuthorizableResourceInterface $resource
	): bool {
		try {
			$assignments = $this->assignmentRepository->getBySubjectId(SubjectId::fromString($subject->subjectId()));
			$rules       = $this->ruleRegistry->getAllApplicableTo($resource);

			foreach ($assignments as $assignment) {
				if ($assignment->isExpired() || !$assignment->isApplicableFor($this->getTenantId($subject->tenantId()))) {
					continue;
				}

				$role = $this->roleRepository->getById($assignment->getRoleId());

				if (!$role->allows($resource->resourceScope(), $action, $resource->resourceType())) {
					continue;
				}

				foreach ($rules as $rule) {
					if (!$rule->isSatisfied($subject, $resource)) {
						continue 2;
					}
				}

				return true;
			}

			return false;
		} catch (RoleNotFoundException) {
			return false;
		}
	}

	/**
	 * Converts the tenant ID string to a TenantId object, or returns null if the tenant ID is null.
	 */
	private function getTenantId(?string $tenantId): ?TenantId
	{
		return null !== $tenantId ? TenantId::fromString($tenantId) : null;
	}

	/**
	 * Generates a cache key based on the given parameters.
	 *
	 * @param string                        $action   the action being performed
	 * @param AuthorizableSubjectInterface  $subject  the subject associated with the action
	 * @param AuthorizableResourceInterface $resource the resource associated with the action
	 *
	 * @return string the generated cache key
	 */
	private function generateCacheKey(
		string $action,
		AuthorizableSubjectInterface $subject,
		AuthorizableResourceInterface $resource
	): string {
		return implode(
			'|',
			[
				$action,
				$subject->subjectId(),
				$resource->resourceScope(),
				$resource->resourceType(),
				$resource->resourceId(),
				$subject->tenantId() ?? 'global',
			]
		);
	}
}
