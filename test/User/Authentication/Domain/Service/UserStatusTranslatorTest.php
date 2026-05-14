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

namespace Webify\Test\User\Authentication\Domain\Service;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authentication\Domain\Service\UserStatusTranslator;
use Webify\User\Authentication\Domain\ValueObject\UserStatus;

/**
 * UserStatusTranslatorTest tests the functionality of the UserStatusTranslator service.
 *
 * @internal
 */
#[CoversClass(UserStatusTranslator::class)]
#[CoversMethod(UserStatusTranslator::class, 'translate')]
final class UserStatusTranslatorTest extends TestCase
{
	private UserStatusTranslator $translator;

	protected function setUp(): void
	{
		$this->translator = new UserStatusTranslator();
	}

	#[Test]
	public function testTranslateActive(): void
	{
		$this->assertTrue($this->translator->translate('active')->isActive());
	}

	#[Test]
	public function testTranslateUnverified(): void
	{
		$result = $this->translator->translate('unverified');

		$this->assertInstanceOf(UserStatus::class, $result);
		$this->assertFalse($result->isActive());
	}

	#[Test]
	public function testTranslateUnknownStatusDefaultsToUnverified(): void
	{
		$result = $this->translator->translate('unknown_status');

		$this->assertInstanceOf(UserStatus::class, $result);
		$this->assertFalse($result->isActive());
	}
}
