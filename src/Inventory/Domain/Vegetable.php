<?php

declare(strict_types=1);

namespace App\Inventory\Domain;

final class Vegetable extends Item
{
    /**
     * @throws \Exception
     */
    public function __construct(ItemName $name, Weight $weight, Unit $unit)
    {
        parent::__construct($name, Category::fromString(Category::VEGETABLE), $weight, $unit);
    }
}
