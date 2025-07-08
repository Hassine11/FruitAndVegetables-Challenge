<?php

namespace App\Tests\Unit\Domain\Item;

use App\Inventory\Domain\Weight;
use App\Inventory\Exception\Item\InvalidIWeightException;
use PHPUnit\Framework\TestCase;

class WeightTest extends TestCase
{
    public function testInvalidWeight()
    {
        $this->expectException(InvalidIWeightException::class);
        Weight::fromGrams(0);
    }

    public function testWeightFromAndInGrams()
    {
        $weightInGrams = Weight::fromGrams(100);
        $this->assertEquals(100, $weightInGrams->value());
        $this->assertEquals(100 / 1000, $weightInGrams->inKilograms()->value());
    }

    public function testWeightFromAndInKilograms()
    {
        $weightFromKilograms = Weight::fromKilograms(2000);
        $this->assertEquals(2000 * 1000, $weightFromKilograms->value());
        $this->assertEquals(2000, $weightFromKilograms->inKilograms()->value());
    }

    public function testInvalidWeightFromUnitAndQuantity()
    {
        $this->expectException(InvalidIWeightException::class);
        Weight::fromUnitAndQuantity('not a valid unit', 1000);
    }

    public function testValidWeightFromUnitAndQuantity()
    {
        $weight = Weight::fromUnitAndQuantity('kg', 1000);
        $this->assertEquals(1000 * 1000, $weight->value());
        $this->assertEquals(1000, $weight->inKilograms()->value());
    }
}
