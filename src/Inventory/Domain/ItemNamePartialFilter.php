<?php

declare(strict_types=1);

namespace App\Inventory\Domain;

#[\AllowDynamicProperties] class ItemNamePartialFilter implements FilterInterface
{
    public function __construct(string $needle)
    {
        $this->needle = $needle;
    }

    #[\Override]
    public function compare(Item $item): bool
    {
        return str_contains($item->Name()->value(), $this->needle);
    }
}
