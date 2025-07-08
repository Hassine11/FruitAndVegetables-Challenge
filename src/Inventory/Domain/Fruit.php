<?php

namespace App\Inventory\Domain;

#[\AllowDynamicProperties] class Fruit extends Item
{
    /**
     * @throws \Exception
     */
    public function __construct(ItemName $name, Weight $weight, Unit $unit)
    {
        parent::__construct($name, Category::fromString(Category::FRUIT), $weight, $unit);
    }
}
