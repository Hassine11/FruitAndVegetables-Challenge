<?php

declare(strict_types=1);

namespace App\Inventory\Factory;

use App\Inventory\Domain\Category;
use App\Inventory\Exception\Item\InvalidCategoryException;

#[\AllowDynamicProperties] class FactoryIdentifier
{
    /**
     * @throws \Exception
     */
    public function __construct(string $type)
    {
        $this->type = Category::fromString($type);
    }

    /**
     * this method identify the factory type based on the item category.
     *
     * @psalm-return InventoryFactory
     *
     * @throws InvalidCategoryException
     */
    public function identify(): InventoryFactory
    {
        return match ($this->type->value()) {
            Category::FRUIT => new FruitFactory(),
            Category::VEGETABLE => new VegetableFactory(),
            default => throw new InvalidCategoryException('Invalid category type'),
        };
    }
}
