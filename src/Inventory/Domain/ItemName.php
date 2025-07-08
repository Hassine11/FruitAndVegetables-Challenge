<?php

declare(strict_types=1);

namespace App\Inventory\Domain;

use App\Inventory\Exception\Item\InvalidItemNameException;

/** @psalm-immutable  */
final class ItemName
{
    private string $value;

    /**
     * @throws InvalidItemNameException
     */
    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidItemNameException('Item name cannot be empty');
        }

        $this->value = $value;
    }

    /**
     * @throws \Exception
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
