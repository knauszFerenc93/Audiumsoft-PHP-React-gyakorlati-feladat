<?php

declare(strict_types=1);

namespace ADS\TakeHome\Filters;

final class RegionFilter implements LocationFilter
{
    public function __construct(
        private readonly string $region,
    ) {
    }

    public function condition(string $cityAlias): string
    {
        return "{$cityAlias}.region = :region";
    }

    public function params(): array
    {
        return [":region" => $this->region];
    }

    public function describe(): string
    {
        return "régió ({$this->region})";
    }
}
