<?php

namespace App\Tests\Unit\Domain\Item;

use App\Inventory\Domain\ItemName;
use App\Inventory\Exception\Item\InvalidItemNameException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemName::class)] class ItemNameTest extends TestCase
{
    public function testInvalidItemName()
    {
        $this->expectException(InvalidItemNameException::class);
        ItemName::fromString('');
    }

    public function testValidItemName()
    {
        $itemName = ItemName::fromString('dummy');
        $this->assertInstanceOf(ItemName::class, $itemName);
        $this->assertEquals('dummy', ItemName::fromString('dummy')->value());
    }
}
