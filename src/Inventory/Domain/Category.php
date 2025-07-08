<?php

declare(strict_types=1);

namespace App\Inventory\Domain;

use App\Inventory\Exception\Item\InvalidCategoryException;

/**
 * @psalm-immutable
 */
final class Category
{
    public const FRUIT = 'fruit';
    public const VEGETABLE = 'vegetable';

    /**
     * @throws \Exception
     */
    private string $value;

    /**
     * @throws InvalidCategoryException
     */
    public function __construct(string $value)
    {
        if (!in_array($value, [self::FRUIT, self::VEGETABLE])) {
            throw new InvalidCategoryException('Invalid Category'.$value.' Category Should be Either fruit Or vegetable');
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

    public function isFruit(): bool
    {
        return self::FRUIT === $this->value;
    }

    public function isVegetable(): bool
    {
        return self::VEGETABLE === $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }
}
