<?php

namespace App\Inventory\Domain;

interface FilterInterface
{
    public function compare(Item $item): bool;
}
