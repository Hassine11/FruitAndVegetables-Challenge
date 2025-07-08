<?php

namespace App\Tests\Unit\Domain\Item;

use App\Inventory\Domain\Category;
use App\Inventory\Domain\Item;
use App\Inventory\Domain\ItemName;
use App\Inventory\Domain\Items;
use App\Inventory\Domain\ItemSplitter;
use App\Inventory\Domain\Unit;
use App\Inventory\Domain\Weight;
use App\Inventory\Exception\Item\InvalidItemsCollectionException;
use App\Tests\DataHelper\TestDataHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Items::class)] class ItemsTest extends TestCase
{
    public function testItem(): void
    {
        $items = TestDataHelper::getTestItems();

        self::assertInstanceOf(Items::class, $items);
        self::assertCount(3, $items);

        $newItem = new Item(
            ItemName::fromString('dummy4'),
            Category::fromString('fruit'),
            Weight::fromUnitAndQuantity('kg', 15),
            Unit::fromString('g'),
        );

        $items->add($newItem);
        self::assertCount(4, $items);

        self::assertIsArray($items->toArray());

        self::assertCount(2, ItemSplitter::fruits($items));
        self::assertCount(2, ItemSplitter::vegetables($items));
    }

    public function testInvalidItemsCollection(): void
    {
        $this->expectException(InvalidItemsCollectionException::class);
        new Items([Category::fromString('fruit'), Weight::fromUnitAndQuantity('kg', 15)]);
    }
}
