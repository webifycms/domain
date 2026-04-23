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

namespace Webify\Test\User\Authorization\Domain\ValueObject;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\User\Authorization\Domain\Exception\InvalidSubjectIdException;
use Webify\User\Authorization\Domain\ValueObject\SubjectId;

/**
 * SubjectIdTest tests the functionality of the SubjectId value object.
 *
 * @internal
 */
#[CoversClass(SubjectId::class)]
#[CoversMethod(SubjectId::class, 'fromString')]
final class SubjectIdTest extends TestCase
{
	#[Test]
	public function testCreateValidSubjectId(): void
	{
		$subjectId = SubjectId::fromString('01ARZ3NDEKTSV4RRFFQ69G5FAV');

		$this->assertInstanceOf(SubjectId::class, $subjectId);
	}

	#[Test]
	public function testInvalidSubjectIdFormatThrowsException(): void
	{
		$this->expectException(InvalidSubjectIdException::class);
		SubjectId::fromString('invalid-id-format');
	}
}
