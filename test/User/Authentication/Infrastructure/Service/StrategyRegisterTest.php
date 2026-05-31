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

namespace Webify\Test\User\Authentication\Infrastructure\Service;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use Webify\Base\Domain\Contract\Authentication\StrategyInterface;
use Webify\User\Authentication\Infrastructure\Exception\StrategyNotFoundException;
use Webify\User\Authentication\Infrastructure\Service\StrategyRegister;

/**
 * Tests for the StrategyRegister.
 *
 * @internal
 */
#[CoversClass(StrategyRegister::class)]
#[CoversMethod(StrategyRegister::class, 'register')]
#[CoversMethod(StrategyRegister::class, 'get')]
#[CoversMethod(StrategyRegister::class, 'getAll')]
final class StrategyRegisterTest extends TestCase
{
	/**
	 * Test that a strategy can be registered and retrieved by its identifier.
	 */
	#[Test]
	public function testRegisterAndGetStrategy(): void
	{
		$strategy = $this->createStub(StrategyInterface::class);

		$strategy
			->method('getIdentifier')
			->willReturn('test_strategy')
		;

		$register = new StrategyRegister();

		$register->register($strategy);
		$this->assertSame($strategy, $register->get('test_strategy'));
	}

	/**
	 * Test that get throws an exception when the strategy is not found.
	 */
	#[Test]
	public function testGetThrowsExceptionWhenStrategyNotFound(): void
	{
		$register = new StrategyRegister();

		$this->expectException(StrategyNotFoundException::class);
		$register->get('non_existent');
	}

	/**
	 * Test that getAll returns all registered strategies.
	 */
	#[Test]
	public function testGetAllReturnsAllRegisteredStrategies(): void
	{
		$strategy1 = $this->createStub(StrategyInterface::class);

		$strategy1
			->method('getIdentifier')
			->willReturn('strategy_1')
		;

		$strategy2 = $this->createStub(StrategyInterface::class);

		$strategy2
			->method('getIdentifier')
			->willReturn('strategy_2')
		;

		$register = new StrategyRegister();

		$register->register($strategy1);
		$register->register($strategy2);

		$all = $register->getAll();

		$this->assertCount(2, $all);
		$this->assertSame($strategy1, $all['strategy_1']);
		$this->assertSame($strategy2, $all['strategy_2']);
	}

	/**
	 * Test that registering a strategy with an existing identifier overwrites it.
	 */
	#[Test]
	public function testRegisterOverwritesExistingStrategy(): void
	{
		$strategy1 = $this->createStub(StrategyInterface::class);

		$strategy1
			->method('getIdentifier')
			->willReturn('test_strategy')
		;

		$strategy2 = $this->createStub(StrategyInterface::class);

		$strategy2
			->method('getIdentifier')
			->willReturn('test_strategy')
		;

		$register = new StrategyRegister();

		$register->register($strategy1);
		$register->register($strategy2);
		$this->assertSame($strategy2, $register->get('test_strategy'));
		$this->assertCount(1, $register->getAll());
	}
}
