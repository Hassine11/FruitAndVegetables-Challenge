<?php

declare(strict_types=1);

namespace App\Inventory\Application\Request\Dto;

use App\Inventory\Domain\Category;
use App\Inventory\Domain\Unit;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class CreateInventoryArticlesRequestDTO implements InventoryRequestDto
{
    #[Assert\NotBlank(message: 'property name is required !')]
    #[Assert\Type(type: 'string')]
    #[Assert\Regex(
        pattern: '/[a-zA-Z]/',
        message: 'Name must contain alphabetic characters.'
    )]
    public string $name;

    #[Assert\NotBlank(message: 'property category is required !')]
    #[Assert\Type('string')]
    #[Assert\Choice(choices: [Category::FRUIT, Category::VEGETABLE], message: 'category must be either "fruit" or "vegetable"')]
    public string $category;

    #[Assert\NotBlank(message: 'property weight is required !')]
    #[Assert\Type(type: 'numeric', message: 'weight must be a number.')]
    #[Assert\GreaterThan(0)]
    public float $weight;

    #[Assert\NotBlank(message: 'property unit is required !')]
    #[Assert\Type('string')]
    #[Assert\Choice(choices: [Unit::GRAM, Unit::KG], message: 'unit must be either "g" or "kg"')]
    public string $unit;

    #[\Override]
    public static function keys(): array
    {
        return ['name', 'category', 'weight', 'unit'];
    }
}
