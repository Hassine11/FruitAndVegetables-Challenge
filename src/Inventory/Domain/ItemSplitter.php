<?php

declare(strict_types=1);

namespace App\Inventory\Domain;

use App\Inventory\Exception\Item\InvalidItemsCollectionException;

final class ItemSplitter
{
    /**
     * @psalm-param Items $items
     *
     * @psalm-return Fruits
     *
     * @throws InvalidItemsCollectionException
     */
    public static function fruits(Items $items): Fruits
    {
        return new Fruits(array_filter($items->toArray(), fn (Item $i) => $i->Category()->isFruit()));
    }

    /**
     * @psalm-param Items $items
     *
     * @psalm-return Vegetables
     *
     * @throws InvalidItemsCollectionException
     */
    public static function vegetables(Items $items): Vegetables
    {
        return new Vegetables(array_filter($items->toArray(), fn (Item $i) => $i->Category()->isVegetable()));
    }
}
