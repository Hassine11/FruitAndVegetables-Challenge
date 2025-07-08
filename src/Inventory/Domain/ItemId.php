<?php

declare(strict_types=1);

namespace App\Inventory\Domain;

use App\Inventory\Exception\Item\InvalidItemIdException;

/**
 * @psalm-immutable
 */
final class ItemId
{
    private int $value;

    /**
     * @throws InvalidItemIdException
     */
    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidItemIdException('ItemId value must be positive');
        }
        $this->value = $value;
    }

    public static function fromInteger(int $value): self
    {
        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }
}
