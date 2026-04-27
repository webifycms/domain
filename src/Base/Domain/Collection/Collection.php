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

namespace Webify\Base\Domain\Collection;

use Countable;
use Iterator;
use Webify\Base\Domain\Exception\{InvalidTypeOfCollectionException, NotExistInCollectionException};

/**
 * Base type-safe collection class.
 *
 * Concrete collections must implement {@see type()} to declare
 * the fully-qualified class/interface name every item must satisfy.
 * All items are validated on insertion, so the collection is always
 * in a consistent state.
 *
 * Example concrete collection:
 *
 *   final class SampleCollection extends Collection
 *   {
 *       protected function type(): string
 *       {
 *           return Sample::class;
 *       }
 *   }
 *
 * @template T of object
 *
 * @implements Iterator<int, T>
 *
 * @phpstan-consistent-constructor
 */
abstract class Collection implements Countable, Iterator
{
	/**
	 * Internal array holding the collection items.
	 *
	 * @var array<int, T>
	 */
	private array $items = [];

	/**
	 * Internal iteration position for the Iterator interface.
	 */
	private int $position = 0;

	/**
	 * Creates a new collection pre-populated with the given items.
	 * Each item is validated against {@see type()} before being added.
	 *
	 * @param array<int, T> $items
	 */
	final public static function from(array $items): static
	{
		/** @var static<T> $collection */
		$collection = new static();

		foreach ($items as $item) {
			$collection->add($item);
		}

		return $collection;
	}

	/**
	 * Returns a new collection with the given item appended.
	 * The original collection is not modified.
	 *
	 * @param T $item
	 */
	final public function with(mixed $item): static
	{
		$clone = clone $this;
		$clone->add($item);

		return $clone;
	}

	/**
	 * Returns a NEW collection with all items matching the predicate removed.
	 * The original collection is not modified.
	 *
	 * @param callable(T): bool $callable
	 */
	final public function without(callable $callable): static
	{
		return static::from(
			array_values(
				array_filter($this->items, static fn (mixed $item): bool => !$callable($item))
			)
		);
	}

	/**
	 * Returns true if the collection contains at least one item matching the predicate.
	 *
	 * @param callable(T): bool $callable
	 */
	final public function contains(callable $callable): bool
	{
		return array_any($this->items, fn ($item) => $callable($item));
	}

	/**
	 * Returns the first item matching the predicate, or null if none matches.
	 *
	 * @param callable(T): bool $callable
	 *
	 * @return null|T
	 */
	final public function find(callable $callable): mixed
	{
		return array_find($this->items, fn ($item) => $callable($item));
	}

	/**
	 * Returns the item at the given zero-based index.
	 *
	 * @return T
	 *
	 * @throws NotExistInCollectionException
	 */
	final public function get(int $index): mixed
	{
		if (!array_key_exists($index, $this->items)) {
			throw new NotExistInCollectionException(
				sprintf('Index %d does not exist in %s.', $index, static::class),
				[
					'index' => $index,
					'class' => static::class,
				]
			);
		}

		return $this->items[$index];
	}

	/**
	 * Returns all items as a plain array.
	 *
	 * @return array<int, T>
	 */
	final public function toArray(): array
	{
		return $this->items;
	}

	/**
	 * Returns true if the collection has no items.
	 */
	final public function isEmpty(): bool
	{
		return [] === $this->items;
	}

	/**
	 * Applies a predicate to each item and returns a new collection containing
	 * only the items for which the predicate returns true.
	 *
	 * @param callable(T): bool $callable
	 */
	final public function filter(callable $callable): static
	{
		return static::from(
			array_values(
				array_filter($this->items, $callable)
			)
		);
	}

	/**
	 * Applies a callback to every item and returns the resulting plain array.
	 * Returns array<int, mixed> because the mapped type may differ from T.
	 *
	 * @param callable(T): mixed $callback
	 *
	 * @return array<int, mixed>
	 */
	final public function map(callable $callback): array
	{
		return array_map($callback, $this->items);
	}

	/**
	 * Reduces the collection to a single value.
	 *
	 * @param callable(mixed, T): mixed $callback
	 */
	final public function reduce(callable $callback, mixed $initial = null): mixed
	{
		return array_reduce($this->items, $callback, $initial);
	}

	/**
	 * Returns a new collection merging this one with another collection of the
	 * same concrete types.
	 *
	 * @param static $other
	 */
	final public function merge(self $other): static
	{
		return static::from(array_merge($this->items, $other->items));
	}

	/**
	 * Returns the total count of the items in the collection.
	 */
	final public function count(): int
	{
		return count($this->items);
	}

	/**
	 * Returns the current item in the iteration.
	 *
	 * @return T
	 */
	final public function current(): mixed
	{
		return $this->items[$this->position];
	}

	/**
	 * Returns the current zero-based index in the iteration.
	 */
	final public function key(): int
	{
		return $this->position;
	}

	/**
	 * Advances the internal pointer to the next item in the iteration.
	 */
	final public function next(): void
	{
		++$this->position;
	}

	/**
	 * Resets the internal pointer to the beginning of the collection.
	 */
	final public function rewind(): void
	{
		$this->position = 0;
	}

	/**
	 * Checks if the current position is valid in the iteration.
	 */
	final public function valid(): bool
	{
		return array_key_exists($this->position, $this->items);
	}

	/**
	 * Returns the fully qualified class or interface name that every item in
	 * this collection must be an instance of.
	 *
	 * @return class-string<T>
	 */
	abstract protected function type(): string;

	/**
	 * Validates and appends a single item to the internal array.
	 * Internal mutation is used only during construction and cloning.
	 *
	 * @param T $item
	 *
	 * @throws InvalidTypeOfCollectionException
	 */
	private function add(mixed $item): void
	{
		$type = $this->type();

		if (!$item instanceof $type) {
			throw new InvalidTypeOfCollectionException(
				sprintf(
					'%s only accepts items of type %s, got %s.',
					static::class,
					$type,
					get_debug_type($item)
				),
				[
					'class'        => static::class,
					'expectedType' => $type,
					'givenType'    => get_debug_type($item),
				]
			);
		}

		$this->items[] = $item;
	}
}
