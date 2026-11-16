<?php

declare(strict_types=1);

namespace ADS\TakeHome\Repositories;

use ADS\TakeHome\Domain\City;
use ADS\TakeHome\Filters\LocationFilter;

interface LocationRepositoryInterface
{
    /**
     * @return list<City>
     */
    public function findCities(LocationFilter $filter): array;

    /**
     * @return list<string>
     */
    public function findRegions(): array;
}
