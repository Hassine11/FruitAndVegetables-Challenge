<?php

namespace App\Inventory\Domain;

use App\Inventory\Exception\Filter\InvalidFilterException;

final class FilterFactory
{
    public const FILTER_NAME = 'name';
    public const FILTER_CATEGORY = 'category';

    /**
     * @throws \Exception
     */
    public static function create(string $filterName, $values): FilterInterface
    {
        return match ($filterName) {
            self::FILTER_NAME => new ItemNamePartialFilter($values),
            self::FILTER_CATEGORY => new CategoryFilter($values),
            default => throw new InvalidFilterException('Unknown filter name'),
        };
    }
}
