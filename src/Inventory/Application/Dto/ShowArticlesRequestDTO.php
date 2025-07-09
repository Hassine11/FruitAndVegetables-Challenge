<?php

declare(strict_types=1);

namespace App\Inventory\Application\Dto;

use App\Inventory\Domain\Category;
use App\Service\Request\RequestDto;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-immutable
 */
final class ShowArticlesRequestDTO implements RequestDto
{
    #[Assert\Type(type: 'string')]
    #[Assert\Regex(
        pattern: '/[a-zA-Z]/',
        message: 'Name must contain alphabetic characters.'
    )]
    public ?string $name = null;

    #[Assert\Choice(choices: [Category::FRUIT, Category::VEGETABLE], message: 'category must be either "fruit" or "vegetable"')]
    #[Assert\Type('string')]
    public ?string $category;

    #[\Override]
    public static function keys(): array
    {
        return ['name', 'category'];
    }
}
