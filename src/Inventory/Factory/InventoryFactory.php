<?php

namespace App\Inventory\Factory;

use App\Inventory\Domain\Item;
use App\Inventory\Domain\ItemName;
use App\Inventory\Domain\Unit;
use App\Inventory\Domain\Weight;

interface InventoryFactory
{
    public function create(ItemName $itemName, Weight $weight, Unit $unit): Item;
}
