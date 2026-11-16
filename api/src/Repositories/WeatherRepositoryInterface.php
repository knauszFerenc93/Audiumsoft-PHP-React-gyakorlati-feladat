<?php

declare(strict_types=1);

namespace ADS\TakeHome\Repositories;

use ADS\TakeHome\Domain\DailyWeather;
use ADS\TakeHome\Domain\DateRange;
use ADS\TakeHome\Filters\LocationFilter;

interface WeatherRepositoryInterface
{
    /**
     * @return list<DailyWeather>
     */
    public function findDailyAverages(LocationFilter $filter, DateRange $range): array;
}
