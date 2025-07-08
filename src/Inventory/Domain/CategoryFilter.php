<?php

namespace App\Inventory\Domain;

#[\AllowDynamicProperties] class CategoryFilter implements FilterInterface
{
    public function __construct(string $needle)
    {
        $this->needle = $needle;
    }

    #[\Override]
    public function compare(Item $item): bool
    {
        return $item->Category()->value() == $this->needle;
    }
}
