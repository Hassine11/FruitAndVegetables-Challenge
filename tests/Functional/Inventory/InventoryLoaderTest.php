<?php

use App\Inventory\Application\Database\InventoryLoader;
use App\Tests\DataHelper\TestDataHelper;
use App\Tests\Functional\FunctionalTestCase;

uses(FunctionalTestCase::class);

beforeEach(/**
 * @throws Doctrine\DBAL\Exception
 */ function () {
    $this->conn = static::getContainer()->get('doctrine.dbal.default_connection');
    $this->inventoryLoader = static::getContainer()->get(InventoryLoader::class);
    TestDataHelper::insertDummyData($this->conn);
});

it('loads items from inventory table', function () {
    $items = $this->inventoryLoader->loadInventory();

    expect($items)->toHaveCount(3);

    $names = array_map(fn ($item) => $item->Name()->value(), iterator_to_array($items));
    expect($names)->toContain('dummy1', 'dummy2', 'dummy3');
});
