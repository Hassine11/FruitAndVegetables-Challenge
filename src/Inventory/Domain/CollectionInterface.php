<?php

namespace App\Inventory\Domain;

interface CollectionInterface
{
    public function add(Item $item);

    public function remove(Item $item);

    public function list();
}
