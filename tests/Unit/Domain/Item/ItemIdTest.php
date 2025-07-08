<?php

namespace App\Tests\Unit\Domain\Item;

use App\Inventory\Domain\ItemId;
use App\Inventory\Exception\Item\InvalidItemIdException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemId::class)] class ItemIdTest extends TestCase
{
    public function testInvalidItemId()
    {
        $this->expectException(InvalidItemIdException::class);
        ItemId::fromInteger(0);
    }

    public function testValidItemId()
    {
        $itemId = ItemId::fromInteger(18);
        $this->assertInstanceOf(ItemId::class, $itemId);
    }
}
