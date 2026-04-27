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

namespace Webify\Test\Base\Domain\Collection;

use PHPUnit\Framework\Attributes\{CoversClass, CoversMethod, Test};
use PHPUnit\Framework\TestCase;
use stdClass;
use Webify\Base\Domain\Collection\Collection;
use Webify\Base\Domain\Exception\{InvalidTypeOfCollectionException, NotExistInCollectionException};
use Webify\Test\Base\Domain\Collection\Fixture\{Item, ItemCollection};

/**
 * CollectionTest tests the functionality of the Collection class.
 *
 * @internal
 */
#[CoversClass(Collection::class)]
#[CoversMethod(Collection::class, 'from')]
#[CoversMethod(Collection::class, 'with')]
#[CoversMethod(Collection::class, 'without')]
#[CoversMethod(Collection::class, 'contains')]
#[CoversMethod(Collection::class, 'find')]
#[CoversMethod(Collection::class, 'get')]
#[CoversMethod(Collection::class, 'toArray')]
#[CoversMethod(Collection::class, 'isEmpty')]
#[CoversMethod(Collection::class, 'filter')]
#[CoversMethod(Collection::class, 'map')]
#[CoversMethod(Collection::class, 'reduce')]
#[CoversMethod(Collection::class, 'merge')]
#[CoversMethod(Collection::class, 'count')]
#[CoversMethod(Collection::class, 'current')]
#[CoversMethod(Collection::class, 'key')]
#[CoversMethod(Collection::class, 'next')]
#[CoversMethod(Collection::class, 'rewind')]
#[CoversMethod(Collection::class, 'valid')]
final class CollectionTest extends TestCase
{
	/**
	 * Test creating a collection with items.
	 */
	#[Test]
	public function fromShouldCreateCollectionWithItems(): void
	{
		$items      = [new Item('one'), new Item('two')];
		$collection = ItemCollection::from($items);

		$this->assertCount(2, $collection);
		$this->assertSame('one', $collection->get(0)->getValue());
		$this->assertSame('two', $collection->get(1)->getValue());
	}

	/**
	 * Test creating a collection with an empty array.
	 */
	#[Test]
	public function fromShouldValidateItemType(): void
	{
		$this->expectException(InvalidTypeOfCollectionException::class);
		// @phpstan-ignore-next-line
		ItemCollection::from(self::createInvalidItemArray());
	}

	/**
	 * Test adding an item to an existing collection.
	 */
	#[Test]
	public function withShouldReturnNewCollectionWithItem(): void
	{
		$original = ItemCollection::from([new Item('one')]);
		$new      = $original->with(new Item('two'));

		$this->assertCount(1, $original);
		$this->assertCount(2, $new);
		$this->assertSame('one', $new->get(0)->getValue());
		$this->assertSame('two', $new->get(1)->getValue());
	}

	/**
	 * Test adding an invalid item to an existing collection.
	 */
	#[Test]
	public function withShouldRejectInvalidItemType(): void
	{
		$this->expectException(InvalidTypeOfCollectionException::class);

		$collection = ItemCollection::from([new Item('one')]);

		/**
		 * @var Item $invalidItem
		 *
		 * @phpstan-ignore-next-line
		 */
		$invalidItem = self::createInvalidItem();

		$collection->with($invalidItem);
	}

	/**
	 * Test removing items from a collection.
	 */
	#[Test]
	public function withoutShouldReturnNewCollectionWithoutMatchingItems(): void
	{
		$collection = ItemCollection::from([
			new Item('one'),
			new Item('two'),
			new Item('three'),
		]);
		$result = $collection->without(fn (Item $item): bool => $item->getValue() === 'two');

		$this->assertCount(3, $collection);
		$this->assertCount(2, $result);
	}

	/**
	 * Test containing items in a collection.
	 */
	#[Test]
	public function containsShouldReturnTrueWhenPredicateMatches(): void
	{
		$collection = ItemCollection::from([new Item('one'), new Item('two')]);

		$this->assertTrue($collection->contains(fn (Item $item): bool => $item->getValue() === 'one'));
	}

	/**
	 * Test not containing items in a collection.
	 */
	#[Test]
	public function containsShouldReturnFalseWhenNoMatch(): void
	{
		$collection = ItemCollection::from([new Item('one')]);

		$this->assertFalse($collection->contains(fn (Item $item): bool => $item->getValue() === 'two'));
	}

	/**
	 * Test finding items in a collection returns the first matching item.
	 */
	#[Test]
	public function findShouldReturnFirstMatchingItem(): void
	{
		$collection = ItemCollection::from([new Item('one'), new Item('two')]);
		$result     = $collection->find(fn (Item $item): bool => $item->getValue() === 'two');

		$this->assertNotNull($result);
		$this->assertSame('two', $result->getValue());
	}

	/**
	 * Test finding items in a collection returns null when no match.
	 */
	#[Test]
	public function findShouldReturnNullWhenNoMatch(): void
	{
		$collection = ItemCollection::from([new Item('one')]);
		$result     = $collection->find(fn (Item $item): bool => $item->getValue() === 'missing');

		$this->assertNull($result);
	}

	/**
	 * Test getting an item from a collection by index.
	 */
	#[Test]
	public function getShouldReturnItemAtIndex(): void
	{
		$collection = ItemCollection::from([new Item('one'), new Item('two')]);

		$this->assertSame('one', $collection->get(0)->getValue());
		$this->assertSame('two', $collection->get(1)->getValue());
	}

	/**
	 * Test throws an exception for an invalid index.
	 */
	#[Test]
	public function getShouldThrowExceptionForInvalidIndex(): void
	{
		$this->expectException(NotExistInCollectionException::class);

		$collection = ItemCollection::from([new Item('one')]);
		$collection->get(5);
	}

	/**
	 * Test converting a collection to an array.
	 */
	#[Test]
	public function toArrayShouldReturnAllItems(): void
	{
		$item1      = new Item('one');
		$item2      = new Item('two');
		$collection = ItemCollection::from([$item1, $item2]);
		$array      = $collection->toArray();

		$this->assertCount(2, $array);
		$this->assertSame($item1, $array[0]);
		$this->assertSame($item2, $array[1]);
	}

	/**
	 * Test checking if a collection is empty.
	 */
	#[Test]
	public function isEmptyShouldReturnTrueWhenEmpty(): void
	{
		$collection = ItemCollection::from([]);

		$this->assertTrue($collection->isEmpty());
	}

	/**
	 * Test checking if a collection is not empty.
	 */
	#[Test]
	public function isEmptyShouldReturnFalseWhenNotEmpty(): void
	{
		$collection = ItemCollection::from([new Item('one')]);

		$this->assertFalse($collection->isEmpty());
	}

	/**
	 * Test filtering items in a collection.
	 */
	#[Test]
	public function filterShouldReturnNewCollectionWithFilteredItems(): void
	{
		$collection = ItemCollection::from([
			new Item('one'),
			new Item('two'),
			new Item('three'),
		]);
		$result = $collection->filter(
			fn (Item $item): bool => $item->getValue() === 'two' || $item->getValue() === 'three'
		);

		$this->assertCount(3, $collection);
		$this->assertCount(2, $result);
		$this->assertSame('two', $result->get(0)->getValue());
		$this->assertSame('three', $result->get(1)->getValue());
	}

	/**
	 * Test mapping items in a collection.
	 */
	#[Test]
	public function mapShouldReturnMappedArray(): void
	{
		$collection = ItemCollection::from([new Item('one'), new Item('two')]);

		$result = $collection->map(fn (Item $item): string => strtoupper($item->getValue()));

		$this->assertSame(['ONE', 'TWO'], $result);
	}

	/**
	 * Test reducing items in a collection.
	 */
	#[Test]
	public function reduceShouldReduceToSingleValue(): void
	{
		$collection = ItemCollection::from([new Item('one'), new Item('two')]);

		$result = $collection->reduce(
			fn (mixed $carry, Item $item): string => $carry . $item->getValue(),
			''
		);

		$this->assertSame('onetwo', $result);
	}

	/**
	 * Test merging two collections.
	 */
	#[Test]
	public function mergeShouldCombineTwoCollections(): void
	{
		$collection1 = ItemCollection::from([new Item('one'), new Item('two')]);
		$collection2 = ItemCollection::from([new Item('three')]);
		$result      = $collection1->merge($collection2);

		$this->assertCount(2, $collection1);
		$this->assertCount(1, $collection2);
		$this->assertCount(3, $result);
		$this->assertSame('one', $result->get(0)->getValue());
		$this->assertSame('two', $result->get(1)->getValue());
		$this->assertSame('three', $result->get(2)->getValue());
	}

	/**
	 * Test count.
	 */
	#[Test]
	public function countShouldReturnItemCount(): void
	{
		$collection = ItemCollection::from([new Item('one'), new Item('two')]);

		$this->assertCount(2, $collection);
	}

	/**
	 * Test iterator.
	 */
	#[Test]
	public function iteratorShouldWorkCorrectly(): void
	{
		$item1      = new Item('one');
		$item2      = new Item('two');
		$collection = ItemCollection::from([$item1, $item2]);
		$items      = [];

		foreach ($collection as $index => $item) {
			$items[$index] = $item;
		}

		$this->assertSame($item1, $items[0]);
		$this->assertSame($item2, $items[1]);
	}

	/**
	 * Test rewind.
	 */
	#[Test]
	public function currentShouldReturnCurrentItem(): void
	{
		$collection = ItemCollection::from([new Item('one'), new Item('two')]);

		$collection->rewind();
		$this->assertSame('one', $collection->current()->getValue());

		$collection->next();
		$this->assertSame('two', $collection->current()->getValue());
	}

	/**
	 * Test next.
	 */
	#[Test]
	public function keyShouldReturnCurrentIndex(): void
	{
		$collection = ItemCollection::from([new Item('one'), new Item('two')]);

		$collection->rewind();
		$this->assertSame(0, $collection->key());

		$collection->next();
		$this->assertSame(1, $collection->key());
	}

	/**
	 * Test valid.
	 */
	#[Test]
	public function validShouldReturnFalseWhenPositionIsInvalid(): void
	{
		$collection = ItemCollection::from([new Item('one')]);

		$collection->rewind();
		$this->assertTrue($collection->valid());

		$collection->next();
		$this->assertFalse($collection->valid());
	}

	/**
	 * @return array<int, stdClass>
	 */
	private static function createInvalidItemArray(): array
	{
		return [new stdClass()];
	}

	private static function createInvalidItem(): stdClass
	{
		return new stdClass();
	}
}
