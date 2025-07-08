<?php

namespace App\Inventory\Domain;

use App\Inventory\Exception\Item\InvalidCategoryException;

class Fruits extends Items
{
    /**
     * @throws InvalidCategoryException
     */
    #[\Override]
    public function add(Item $item): void
    {
        if (!$item->Category()->isFruit()) {
            throw new InvalidCategoryException('Only fruits can be added to Fruits collection.');
        }

        parent::add($item);
    }
}
