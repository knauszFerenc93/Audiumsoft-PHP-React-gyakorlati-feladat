<?php

declare(strict_types=1);

namespace ADS\TakeHome\Filters;

final class CityFilter implements LocationFilter
{
    public function __construct(
        private readonly int $cityId,
    ) {
    }

    public function condition(string $cityAlias): string
    {
        return "{$cityAlias}.id = :cityId";
    }

    public function params(): array
    {
        return [":cityId" => $this->cityId];
    }

    public function describe(): string
    {
        return "város (id: {$this->cityId})";
    }
}
