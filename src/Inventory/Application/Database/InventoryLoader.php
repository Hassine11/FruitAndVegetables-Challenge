<?php

declare(strict_types=1);

namespace App\Inventory\Application\Database;

use App\Inventory\Domain\Category;
use App\Inventory\Domain\Item;
use App\Inventory\Domain\ItemId;
use App\Inventory\Domain\ItemName;
use App\Inventory\Domain\Items;
use App\Inventory\Domain\Unit;
use App\Inventory\Domain\Weight;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class InventoryLoader
{
    private Connection $connection;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * this method load all inventory items and build Items object.
     *
     * @psalm-return Items
     *
     * @throws Exception
     * @throws \Exception
     */
    public function loadInventory(): Items
    {
        $sql = <<<SQL
SELECT *
FROM inventory
ORDER BY ID DESC
SQL;
        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();

        $items = [];
        foreach ($rows as $row) {
            $items[] = new Item(
                ItemName::fromString($row['article_name']),
                Category::fromString($row['category']),
                Weight::fromGrams($row['quantity']),
                Unit::fromString($row['unit']),
                ItemId::fromInteger($row['id']),
            );
        }

        return new Items($items);
    }
}
