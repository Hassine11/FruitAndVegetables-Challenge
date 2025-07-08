<?php

declare(strict_types=1);

namespace App\Repository;

use App\Inventory\Domain\Item;
use App\Inventory\Domain\ItemName;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class InventoryRepository
{
    private const TABLE_NAME = 'inventory';

    private Connection $connection;

    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @psalm-param ItemName $articleName
     *
     * @psalm-return array{
     *     id:int,
     *     article_name:string,
     *     category:string,
     *     unit:string,
     *     quantity:integer,
     *     created_at:string,
     *     updated_at:string
     * }
     *
     * @throws
     */
    public function getInventoryByArticleName(ItemName $articleName): array|false
    {
        $query = $this->connection->createQueryBuilder();

        $query->select('*')
            ->from(self::TABLE_NAME)
            ->where('article_name = :articleName')
            ->setMaxResults(1)
            ->setParameter('articleName', $articleName->value());

        return $query->executeQuery()->fetchAssociative();
    }

    /**
     * this method persist Item to database table inventory and return created item id.
     *
     * @psalm-param Item $item
     *
     * @throws Exception
     */
    public function storeInventoryArticle(Item $item): int
    {
        $this->connection->insert(self::TABLE_NAME, [
            'article_name' => $item->Name()->value(),
            'category' => $item->Category()->value(),
            'unit' => $item->Unit()->value(),
            'quantity' => $item->Weight()->inGrams()->value(),
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * this method checks if desired item exists by name in table inventory and return bool.
     *
     * @psalm-param ItemName $itemName
     *
     * @psalm-return bool
     *
     * @throws Exception
     */
    public function itemExists(ItemName $itemName): bool
    {
        return false !== $this->getInventoryByArticleName($itemName);
    }
}
