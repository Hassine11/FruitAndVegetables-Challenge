<?php

namespace App\Inventory\Domain;

class Item
{
    private ItemName $itemName;
    private Unit $unit;
    private Weight $weight;
    private Category $category;

    private ?ItemId $itemId;

    public function __construct(
        ItemName $itemName,
        Category $category,
        Weight $weight,
        Unit $unit,
        ?ItemId $itemId = null,
    ) {
        $this->itemName = $itemName;
        $this->unit = $unit;
        $this->weight = $weight;
        $this->category = $category;
        $this->itemId = $itemId;
    }

    public function Name(): ItemName
    {
        return $this->itemName;
    }

    public function Category(): Category
    {
        return $this->category;
    }

    public function Weight(): Weight
    {
        return $this->weight;
    }

    public function Unit(): Unit
    {
        return $this->unit;
    }

    public function Id(): ItemId
    {
        return $this->itemId;
    }
}
