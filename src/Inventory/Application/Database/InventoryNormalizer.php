<?php

declare(strict_types=1);

namespace App\Inventory\Application\Database;

use App\Inventory\Domain\Items;
use App\Inventory\Domain\ItemSplitter;
use App\Inventory\Domain\Unit;

class InventoryNormalizer
{
    /**
     * this method does the normalization for the response rendering.
     *
     * @psalm-param  Items $items
     * @psalm-param  string $unit
     *
     * @psalm-return  array{fruits:array,vegetables:array,totalItems: integer}
     */
    public function normalize(Items $items, string $unit = Unit::GRAM): array
    {
        $fruits = ItemSplitter::fruits($items);
        $vegetables = ItemSplitter::vegetables($items);

        return [
            'fruits' => $this->normalizeCollection($fruits, $unit),
            'vegetables' => $this->normalizeCollection($vegetables, $unit),
            'totalItems' => $items->count(),
        ];
    }

    /**
     * @psalm-param Items $items
     * @psalm-param  string $unit
     *
     * @psalm-return array{id:integer,name:string,weight:integer,unit:string}
     */
    private function normalizeCollection(Items $items, string $unit): array
    {
        $result = [];
        foreach ($items->list() as $item) {
            $result[] = [
                'id' => $item->Id()->value(),
                'name' => $item->Name()->value(),
                'weight' => Unit::GRAM === $unit
                    ? $item->Weight()->inGrams()->value()
                    : $item->Weight()->inKilograms()->value(),
                'unit' => $unit,
            ];
        }

        return $result;
    }
}
