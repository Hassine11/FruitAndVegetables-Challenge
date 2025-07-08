<?php

declare(strict_types=1);

namespace App\Inventory\Domain;

use App\Inventory\Exception\Item\InvalidIUnitException;

/**
 * @psalm-immutable
 */
final class Unit
{
    public const GRAM = 'g';
    public const KG = 'kg';

    /**
     * @throws \Exception
     */
    private string $value;

    /**
     * @throws InvalidIUnitException
     */
    public function __construct(string $value)
    {
        if (self::GRAM != $value) {
            throw new InvalidIUnitException('Invalid Unit'.$value.' Unit Must be in g (gram)');
        }

        $this->value = $value;
    }

    /**
     * @throws InvalidIUnitException
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
