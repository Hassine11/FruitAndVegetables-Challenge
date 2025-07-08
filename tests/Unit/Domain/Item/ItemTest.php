<?php

namespace App\Tests\Unit\Domain\Item;

use App\Inventory\Domain\Category;
use App\Inventory\Domain\Item;
use App\Inventory\Domain\ItemId;
use App\Inventory\Domain\ItemName;
use App\Inventory\Domain\Unit;
use App\Inventory\Domain\Weight;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Item::class)] class ItemTest extends TestCase
{
    public function testItem()
    {
        $item = new Item(
            ItemName::fromString('dummy'),
            Category::fromString('fruit'),
            Weight::fromUnitAndQuantity('g', 1000),
            Unit::fromString('g'),
            ItemId::fromInteger(1)
        );

        $this->assertInstanceOf(Item::class, $item);
    }
}
