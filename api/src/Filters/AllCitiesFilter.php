<?php

declare(strict_types=1);

namespace ADS\TakeHome\Filters;

final class AllCitiesFilter implements LocationFilter
{
    public function condition(string $cityAlias): string
    {
        return "TRUE";
    }

    public function params(): array
    {
        return [];
    }

    public function describe(): string
    {
        return "összes város";
    }
}
