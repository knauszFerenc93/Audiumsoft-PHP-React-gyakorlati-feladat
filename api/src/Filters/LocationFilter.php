<?php

declare(strict_types=1);

namespace ADS\TakeHome\Filters;

interface LocationFilter
{
    public function condition(string $cityAlias): string;

    /**
     * @return array<string, scalar>
     */
    public function params(): array;

    public function describe(): string;
}
