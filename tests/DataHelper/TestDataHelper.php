<?php

namespace App\Tests\DataHelper;

use App\Inventory\Domain\Category;
use App\Inventory\Domain\Item;
use App\Inventory\Domain\ItemName;
use App\Inventory\Domain\Items;
use App\Inventory\Domain\Unit;
use App\Inventory\Domain\Weight;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestDataHelper extends KernelTestCase
{
    public static function getTestItems(): Items
    {
        $item1 = new Item(
            ItemName::fromString('dummy1'),
            Category::fromString('fruit'),
            Weight::fromUnitAndQuantity('g', 1000),
            Unit::fromString('g'),
        );

        $item2 = new Item(
            ItemName::fromString('dummy2'),
            Category::fromString('vegetable'),
            Weight::fromUnitAndQuantity('kg', 26),
            Unit::fromString('g'),
        );

        $item3 = new Item(
            ItemName::fromString('dummy3'),
            Category::fromString('vegetable'),
            Weight::fromUnitAndQuantity('kg', 20),
            Unit::fromString('g'),
        );

        return new Items([$item1, $item2, $item3]);
    }

    /**
     * @throws Exception
     */
    public static function insertDummyData(Connection $conn): void
    {
        $conn->executeStatement('DELETE FROM inventory');

        foreach (self::getTestItems() as $testItem) {
            $conn->insert('inventory', [
                'article_name' => $testItem->Name()->value(),
                'category' => $testItem->Category()->value(),
                'quantity' => $testItem->Weight()->value(),
                'unit' => $testItem->Unit()->value(),
            ]);
        }
    }
}
