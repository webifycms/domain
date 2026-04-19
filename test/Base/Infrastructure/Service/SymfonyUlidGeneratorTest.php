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

namespace Webify\Test\Base\Infrastructure\Service;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;
use Webify\Base\Domain\Service\UniqueIdGeneratorInterface;
use Webify\Base\Infrastructure\Service\SymfonyUlidGenerator;

/**
 * SymfonyUlidGeneratorTest tests the functionality of the SymfonyUlidGenerator class.
 *
 * @internal
 */
#[CoversClass(SymfonyUlidGenerator::class)]
#[CoversMethod(SymfonyUlidGenerator::class, 'generate')]
#[CoversMethod(SymfonyUlidGenerator::class, 'generateFrom')]
final class SymfonyUlidGeneratorTest extends TestCase
{
	/**
	 * Regular expression pattern for validating ULID Base32 format.
	 * ULIDs are 26-character strings that start with a character in the range 0-7,
	 * followed by 25 characters that can be digits (0-9) or uppercase letters (A-H, J-K, M-N, P-T, V-Z).
	 */
	private const string PATTERN_FORMAT = '/^[0-7][0-9A-HJKMNP-TV-Z]{25}$/i';

	/**
	 * The generator instance to test.
	 */
	private SymfonyUlidGenerator $generator;

	/**
	 * Set up the test fixture.
	 */
	protected function setUp(): void
	{
		$this->generator = new SymfonyUlidGenerator();
	}

	/**
	 * Test that the generator implements the interface.
	 */
	#[Test]
	public function testImplementsUniqueIdGeneratorInterface(): void
	{
		$this->assertInstanceOf(UniqueIdGeneratorInterface::class, $this->generator);
	}

	/**
	 * Test generate method returns a valid ULID string.
	 */
	#[Test]
	public function testGenerateReturnsValidUlidString(): void
	{
		$ulid = $this->generator->generate();

		// Should be 26 characters long (ULID Base32 format)
		$this->assertSame(26, strlen($ulid));
		// Should match ULID Base32 format
		$this->assertMatchesRegularExpression(self::PATTERN_FORMAT, $ulid);
	}

	/**
	 * Test generate method produces unique IDs.
	 */
	#[Test]
	public function testGenerateProducesUniqueIds(): void
	{
		$ulid1 = $this->generator->generate();
		$ulid2 = $this->generator->generate();
		$ulid3 = $this->generator->generate();

		// ULIDs should be unique
		$this->assertNotSame($ulid1, $ulid2);
		$this->assertNotSame($ulid2, $ulid3);
		$this->assertNotSame($ulid1, $ulid3);
	}

	/**
	 * Test generate method produces sortable IDs.
	 */
	#[Test]
	public function testGenerateProducesSortableIds(): void
	{
		$ulid1 = $this->generator->generate();

		usleep(1000); // Sleep for 1ms to ensure time difference

		$ulid2 = $this->generator->generate();

		// Later ULID should be greater than earlier ULID (lexicographically)
		$this->assertGreaterThan($ulid1, $ulid2);
	}

	/**
	 * Test generateFrom method converts a valid ULID string.
	 */
	#[Test]
	public function testGenerateFromConvertsValidUlidString(): void
	{
		// Create a valid ULID
		$validUlid = '01ARZ3NDEKTSV4RRFFQ69G5FAV';
		$result    = $this->generator->generateFrom($validUlid);

		// Result should be 26 characters
		$this->assertSame(26, strlen($result));
		// Result should match ULID format
		$this->assertMatchesRegularExpression(self::PATTERN_FORMAT, $result);
	}

	/**
	 * Test generateFrom method with lowercase input.
	 */
	#[Test]
	public function testGenerateFromWithLowercaseInput(): void
	{
		$lowercaseUlid = '01arz3ndektsv4rrffq69g5fav';
		$result        = $this->generator->generateFrom($lowercaseUlid);

		// Should return a valid ULID string
		$this->assertMatchesRegularExpression(self::PATTERN_FORMAT, $result);
	}

	/**
	 * Test generateFrom method with mixed case input.
	 */
	#[Test]
	public function testGenerateFromWithMixedCaseInput(): void
	{
		$mixedCaseUlid = '01ArZ3nDeKtSv4RrFfQ69g5FaV';
		$result        = $this->generator->generateFrom($mixedCaseUlid);

		// Should return a valid ULID string
		$this->assertMatchesRegularExpression(self::PATTERN_FORMAT, $result);
	}

	/**
	 * Test generateFrom method throws exception for invalid ULID.
	 */
	#[Test]
	public function testGenerateFromThrowsExceptionForInvalidUlid(): void
	{
		$this->expectException(InvalidArgumentException::class);

		$this->generator->generateFrom('invalid-ulid-string');
	}

	/**
	 * Test generateFrom method throws exception for invalid first character.
	 */
	#[Test]
	public function testGenerateFromThrowsExceptionForInvalidFirstCharacter(): void
	{
		$this->expectException(InvalidArgumentException::class);

		// First character must be 0-7
		$this->generator->generateFrom('81ARZ3NDEKTSV4RRFFQ69G5FAV');
	}

	/**
	 * Test generateFrom method with actual Ulid object string representation.
	 */
	#[Test]
	public function testGenerateFromWithSymfonyUlidObjectString(): void
	{
		// Create a Ulid using Symfony and get its Base32 representation
		$symphonyUlid = new Ulid();
		$ulidString   = $symphonyUlid->toBase32();
		$result       = $this->generator->generateFrom($ulidString);

		// Should successfully convert and return a valid ULID
		$this->assertMatchesRegularExpression(self::PATTERN_FORMAT, $result);
	}

	/**
	 * Test that generate and generateFrom both return valid ULID format.
	 */
	#[Test]
	public function testBothMethodsReturnValidFormat(): void
	{
		$generated  = $this->generator->generate();
		$fromString = $this->generator->generateFrom($generated);

		// Both should be valid ULID strings
		$this->assertMatchesRegularExpression(self::PATTERN_FORMAT, $generated);
		$this->assertMatchesRegularExpression(self::PATTERN_FORMAT, $fromString);
	}
}
