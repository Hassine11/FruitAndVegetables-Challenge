<?php

namespace App\Inventory\Domain;

use App\Inventory\Exception\Item\InvalidItemsCollectionException;

/** @implements \IteratorAggregate<int, Item> */
class Items implements \IteratorAggregate, \Countable, CollectionInterface
{
    /**
     * @var Item[]
     */
    private array $items;

    /**
     * @param Item[] $items
     *
     * @throws InvalidItemsCollectionException
     */
    public function __construct(array $items)
    {
        foreach ($items as $item) {
            if (!$item instanceof Item) {
                throw new InvalidItemsCollectionException('All elements must be instances of Item');
            }
        }
        $this->items = $items;
    }

    #[\Override]
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    #[\Override]
    public function count(): int
    {
        return count($this->items);
    }

    #[\Override]
    public function add(Item $item): void
    {
        $newItems = $this->items;
        $newItems[] = $item;

        $this->items = $newItems;
    }

    #[\Override]
    /** @psalm-suppress PossiblyUnusedMethod */
    public function remove(Item $item): void
    {
        $filtered = array_filter($this->items, fn (Item $element) => $element->Id()->value() !== $item->Id()->value());

        $this->items = array_values($filtered);
    }

    #[\Override]
    public function list(): self
    {
        return new self($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
