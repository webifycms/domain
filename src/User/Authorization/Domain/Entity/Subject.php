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

namespace Webify\User\Authorization\Domain\Entity;

use Webify\Base\Domain\Entity\AggregateRoot;
use Webify\User\Authorization\Domain\ValueObject\{SubjectAttributes, SubjectId, SubjectType, TenantId};

/**
 * Subject aggregate root defines what it needs to know about an actor of the authorization.
 */
final class Subject extends AggregateRoot
{
	/**
	 * Private constructor enforces the use of the factory methods to initiate this aggregate root.
	 *
	 * @param SubjectId         $id         the unique identifier of the subject
	 * @param SubjectType       $type       the type of the subject
	 * @param SubjectAttributes $attributes the attributes of the subject
	 * @param null|TenantId     $tenantId   the tenant unique identifier of the subject
	 */
	private function __construct(
		private readonly SubjectId $id,
		private readonly SubjectType $type,
		private readonly SubjectAttributes $attributes,
		private readonly ?TenantId $tenantId = null
	) {}

	/**
	 * Gets the ID.
	 */
	public function getId(): SubjectId
	{
		return $this->id;
	}

	/**
	 * Gets the type.
	 */
	public function getType(): SubjectType
	{
		return $this->type;
	}

	/**
	 * Get the attributes.
	 */
	public function getAttributes(): SubjectAttributes
	{
		return $this->attributes;
	}

	/**
	 * Get the tenant ID.
	 */
	public function getTenantId(): ?TenantId
	{
		return $this->tenantId;
	}

	/**
	 * Checks if the given attribute key exists.
	 *
	 * @param string $key the key of the attribute to check for existence
	 *
	 * @return bool returns true if the attribute exists, false otherwise
	 */
	public function hasAttribute(string $key): bool
	{
		return $this->attributes->get($key) !== [];
	}

	/**
	 * Gets the attribute for the given key.
	 *
	 * @return array<string, string>|array{}
	 */
	public function getAttribute(string $key): array
	{
		return $this->attributes->get($key);
	}

	/**
	 * Create a new instance of the subject.
	 */
	public static function create(
		SubjectId $id,
		SubjectType $type,
		SubjectAttributes $attributes,
		?TenantId $tenantId = null
	): self {
		return new self($id, $type, $attributes, $tenantId);
	}
}
