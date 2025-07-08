<?php

namespace App\Tests\Unit\Domain\Item;

use App\Inventory\Domain\Category;
use App\Inventory\Exception\Item\InvalidCategoryException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Category::class)] class CategoryTest extends TestCase
{
    public function testInvalidCategory()
    {
        $this->expectException(InvalidCategoryException::class);
        Category::fromString('not a category');
    }

    public function testValidCategory()
    {
        $category = Category::fromString('fruit');
        $this->assertInstanceOf(Category::class, $category);
        $category = Category::fromString('vegetable');
        $this->assertInstanceOf(Category::class, $category);
    }
}
