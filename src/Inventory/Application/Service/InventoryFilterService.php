<?php

declare(strict_types=1);

namespace App\Inventory\Application\Service;

use App\Inventory\Application\Request\Dto\InventoryRequestDto;
use App\Inventory\Domain\FilterFactory;
use App\Inventory\Domain\Items;

class InventoryFilterService
{
    /**
     * this method apply filters on items and return Items after.
     *
     * @psalm-param InventoryRequestDto $filters
     * @psalm-param Items $items
     *
     * @psalm-return Items
     *
     * @throws \Exception
     */
    public function applyFilters(InventoryRequestDto $filters, Items $items): Items
    {
        foreach ($filters as $key => $value) {
            if (!in_array($key, [
                'name',
                'category',
            ], true) || null === $value) {
                continue;
            }

            $filter = FilterFactory::create($key, $value);

            //  $items = array_filter($items, [$filter, 'compare']);

            foreach ($items->toArray() as $item) {
                if (!$filter->compare($item)) {
                    $items->remove($item);
                }
            }
        }

        return $items;
    }
}
