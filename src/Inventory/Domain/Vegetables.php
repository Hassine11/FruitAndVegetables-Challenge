<?php

namespace App\Inventory\Domain;

use App\Inventory\Exception\Item\InvalidCategoryException;

class Vegetables extends Items
{
    #[\Override]
    public function add(Item $item): void
    {
        if (!$item->Category()->isVegetable()) {
            throw new InvalidCategoryException('Only vegetables can be added to Vegetables collection.');
        }

        parent::add($item);
    }
}
