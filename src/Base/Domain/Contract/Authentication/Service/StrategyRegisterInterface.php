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

namespace Webify\Base\Domain\Contract\Authentication\Service;

use RuntimeException;
use Webify\Base\Domain\Contract\Authentication\StrategyInterface;

/**
 * The contract for registering authentication strategies.
 */
interface StrategyRegisterInterface
{
	/**
	 * Register an authentication strategy to the system.
	 * When registering a strategy, it should use its identifier as the key.
	 */
	public function register(StrategyInterface $strategy): void;

	/**
	 * Returns an authentication strategy by its identifier.
	 *
	 * @throws RuntimeException if the strategy is not found
	 */
	public function get(string $identifier): StrategyInterface;

	/**
	 * Returns all registered authentication strategies.
	 * The returned array should be indexed by the strategy identifier.
	 *
	 * @return array<string, StrategyInterface>
	 */
	public function getAll(): array;
}
