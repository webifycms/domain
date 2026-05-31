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

namespace Webify\User\Authentication\Infrastructure\Service;

use Webify\Base\Domain\Contract\Authentication\Service\StrategyRegisterInterface;
use Webify\Base\Domain\Contract\Authentication\StrategyInterface;
use Webify\User\Authentication\Infrastructure\Exception\StrategyNotFoundException;

/**
 * Implementation of the StrategyRegisterInterface.
 */
final class StrategyRegister implements StrategyRegisterInterface
{
	/**
	 * The store of registered strategies.
	 * 1. The key is the identifier of the strategy.
	 * 2. The value is the strategy instance.
	 *
	 * @var array <string, StrategyInterface>
	 */
	private array $store = [];

	/**
	 * {@inheritDoc}
	 */
	public function register(StrategyInterface $strategy): void
	{
		$this->store[$strategy->getIdentifier()] = $strategy;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(string $identifier): StrategyInterface
	{
		if (!$this->has($identifier)) {
			throw StrategyNotFoundException::forIdentifier($identifier);
		}

		return $this->store[$identifier];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAll(): array
	{
		return $this->store;
	}

	/**
	 * Check if the strategy with the given identifier is registered.
	 */
	private function has(string $identifier): bool
	{
		return array_key_exists($identifier, $this->store);
	}
}
