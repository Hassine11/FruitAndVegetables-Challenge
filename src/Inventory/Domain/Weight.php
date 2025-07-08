<?php

declare(strict_types=1);

namespace App\Inventory\Domain;

use App\Inventory\Exception\Item\InvalidIWeightException;

/**
 * @psalm-immutable
 */
final class Weight
{
    private float $value;

    /**
     * @throws InvalidIWeightException
     */
    private function __construct(float $value)
    {
        if ($value <= 0) {
            throw new InvalidIWeightException('Weight should be greater than 0');
        }
        $this->value = $value;
    }

    public static function fromGrams(float $value): self
    {
        return new self($value);
    }

    public static function fromKilograms(float $value): self
    {
        return new self($value * 1000);
    }

    public function inGrams(): self
    {
        return new self($this->value);
    }

    public function inKilograms(): self
    {
        return new self($this->value / 1000);
    }

    /**
     * @throws InvalidIWeightException
     */
    public static function fromUnitAndQuantity(string $unit, float $quantity): self
    {
        if (!in_array($unit, [Unit::GRAM, Unit::KG])) {
            throw new InvalidIWeightException('Invalid weight unit : '.$unit);
        }

        return match ($unit) {
            Unit::GRAM => self::fromGrams($quantity),
            Unit::KG => self::fromKilograms($quantity),
        };
    }

    public function value(): float
    {
        return $this->value;
    }
}
