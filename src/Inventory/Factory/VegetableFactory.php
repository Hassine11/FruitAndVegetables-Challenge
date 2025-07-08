<?php

declare(strict_types=1);

namespace App\Inventory\Factory;

use App\Inventory\Domain\Item;
use App\Inventory\Domain\ItemName;
use App\Inventory\Domain\Unit;
use App\Inventory\Domain\Vegetable;
use App\Inventory\Domain\Weight;

class VegetableFactory implements InventoryFactory
{
    /**
     * this method create Vegetable Item.
     *
     * @psalm-param  ItemName $itemName
     * @psalm-param Weight $weight
     * @psalm-param Unit $unit
     *
     * @psalm-return  Item
     *
     * @throws \Exception
     */
    #[\Override]
    public function create(ItemName $itemName, Weight $weight, Unit $unit): Item
    {
        return new Vegetable(
            $itemName, $weight, $unit
        );
    }
}
