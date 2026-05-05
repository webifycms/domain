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

namespace Webify\Test\User\Authorization\Domain\Service;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Contract\Authorization\{AuthorizableResourceInterface, AuthorizableSubjectInterface};
use Webify\Base\Domain\Exception\AccessDeniedException;
use Webify\Base\Domain\Service\Authorization\AuthorizationInterface;
use Webify\User\Authorization\Domain\Service\CheckAccess;

/**
 * CheckAccessTest tests the functionality of the CheckAccess service.
 *
 * @internal
 */
#[CoversClass(CheckAccess::class)]
#[CoversMethod(CheckAccess::class, '__construct')]
#[CoversMethod(CheckAccess::class, 'isAllowed')]
#[CoversMethod(CheckAccess::class, 'denyUnless')]
final class CheckAccessTest extends TestCase
{
	/**
	 * Tests that the method isAllowed returns true when the authorization check passes.
	 */
	#[Test]
	public function testIsAllowedReturnsTrueWhenAuthorizationCheckPasses(): void
	{
		$authorization = $this->createStub(AuthorizationInterface::class);

		$authorization->method('check')
			->willReturn(true)
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FAV')
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FBW')
		;

		$service = new CheckAccess($authorization);

		$this->assertTrue($service->isAllowed('read', $subject, $resource));
	}

	/**
	 * Tests that the method isAllowed returns false when the authorization check fails.
	 */
	#[Test]
	public function testIsAllowedReturnsFalseWhenAuthorizationCheckFails(): void
	{
		$authorization = $this->createStub(AuthorizationInterface::class);

		$authorization->method('check')
			->willReturn(false)
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FAV')
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FBW')
		;

		$service = new CheckAccess($authorization);

		$this->assertFalse($service->isAllowed('delete', $subject, $resource));
	}

	/**
	 * Tests that the `isAllowed` method delegates to the `check` method of the injected
	 * `AuthorizationInterface` implementation, verifying access permissions based on
	 * the provided action, subject, and resource.
	 */
	#[Test]
	public function testIsAllowedDelegatesToAuthorizationCheck(): void
	{
		$authorization = $this->createMock(AuthorizationInterface::class);

		$authorization->expects($this->once())
			->method('check')
			->with(
				'update',
				$this->isInstanceOf(AuthorizableSubjectInterface::class),
				$this->isInstanceOf(AuthorizableResourceInterface::class)
			)
			->willReturn(true)
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FAV')
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FBW')
		;

		$service = new CheckAccess($authorization);

		$service->isAllowed('update', $subject, $resource);
	}

	/**
	 * Tests that the `denyUnless` method does not throw an exception when access is allowed.
	 *
	 * This test verifies that the `CheckAccess` service correctly proceeds without error
	 * when the `AuthorizationInterface` authorizes the requested action for the specified
	 * subject and resource.
	 */
	#[Test]
	public function testDenyUnlessDoesNotThrowWhenAccessIsAllowed(): void
	{
		$authorization = $this->createStub(AuthorizationInterface::class);

		$authorization->method('check')
			->willReturn(true)
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FAV')
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FBW')
		;

		$service = new CheckAccess($authorization);

		$this->expectNotToPerformAssertions();
		$service->denyUnless('read', $subject, $resource);
	}

	/**
	 * Tests that the `denyUnless` method throws an `AccessDeniedException` when access is denied.
	 *
	 * This test verifies that the `CheckAccess` service correctly throws an `AccessDeniedException`
	 * when the `AuthorizationInterface` denies the requested action for the specified subject and resource.
	 */
	#[Test]
	public function testDenyUnlessThrowsAccessDeniedExceptionWhenAccessIsDenied(): void
	{
		$authorization = $this->createStub(AuthorizationInterface::class);

		$authorization->method('check')
			->willReturn(false)
		;

		$subjectId  = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$resourceId = '01ARZ3NDEKTSV4RRFFQ69G5FBW';
		$subject    = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn($subjectId)
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceId')
			->willReturn($resourceId)
		;

		$service = new CheckAccess($authorization);

		$this->expectException(AccessDeniedException::class);
		$this->expectExceptionMessage(
			sprintf(
				'Access denied for action "delete" on subject "%s" and resource "%s".',
				$subjectId,
				$resourceId
			)
		);
		$service->denyUnless('delete', $subject, $resource);
	}

	/**
	 * Tests that the `denyUnless` method delegates to the `AuthorizationInterface` for permission checks.
	 *
	 * This test ensures that the `CheckAccess` service calls the `check` method on the `AuthorizationInterface`
	 * with the expected action, subject, and resource. It verifies that the `check` method is invoked exactly
	 * once and returns `true` to allow the requested action.
	 */
	#[Test]
	public function testDenyUnlessDelegatesToAuthorizationCheck(): void
	{
		$authorization = $this->createMock(AuthorizationInterface::class);

		$authorization->expects($this->once())
			->method('check')
			->with(
				'manage',
				$this->isInstanceOf(AuthorizableSubjectInterface::class),
				$this->isInstanceOf(AuthorizableResourceInterface::class)
			)
			->willReturn(true)
		;

		$subject = $this->createStub(AuthorizableSubjectInterface::class);

		$subject->method('subjectId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FAV')
		;

		$resource = $this->createStub(AuthorizableResourceInterface::class);

		$resource->method('resourceId')
			->willReturn('01ARZ3NDEKTSV4RRFFQ69G5FBW')
		;

		$service = new CheckAccess($authorization);

		$service->denyUnless('manage', $subject, $resource);
	}
}
