<?php

declare(strict_types=1);

namespace App\Inventory\Application\Service;

use App\Inventory\Application\Request\Dto\CreateInventoryArticlesRequestDTO;
use App\Inventory\Domain\ItemName;
use App\Inventory\Domain\Items;
use App\Inventory\Domain\Unit;
use App\Inventory\Domain\Weight;
use App\Inventory\Factory\FactoryIdentifier;

class InventoryService
{
    /**
     * this method build Items from Payload Based on Their Category
     * it uses Factory to Identify which category Item.
     *
     * @psalm-param CreateInventoryArticlesRequestDTO[] $payload
     *
     * @psalm-return Items
     *
     * @throws \Exception
     */
    public function buildItemsFromPayload(array $payload): Items
    {
        $items = array_map(function ($item) {
            return (new FactoryIdentifier($item->category))->identify()->create(
                ItemName::fromString($item->name),
                Weight::fromUnitAndQuantity($item->unit, $item->weight),
                Unit::fromString(Unit::GRAM),
            );
        }, $payload);

        return new Items($items);
    }
}
