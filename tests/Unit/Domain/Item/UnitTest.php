<?php

namespace App\Tests\Unit\Domain\Item;

use App\Inventory\Domain\Unit;
use App\Inventory\Exception\Item\InvalidIUnitException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Unit::class)] class UnitTest extends TestCase
{
    public function testInvalidCategory()
    {
        $this->expectException(InvalidIUnitException::class);
        Unit::fromString('not a unit');
    }

    public function testValidUnit()
    {
        $unit = Unit::fromString('g');
        $this->assertInstanceOf(Unit::class, $unit);
    }
}
