<?php

declare(strict_types=1);

namespace ADS\TakeHome\Repositories;

use ADS\TakeHome\Domain\DailyWeather;
use ADS\TakeHome\Domain\DateRange;
use ADS\TakeHome\Filters\LocationFilter;
use ADS\TakeHome\Framework\MySQL;

final class MySqlWeatherRepository implements WeatherRepositoryInterface
{
    private const MISSING_VALUE = -999;

    public function __construct(
        private readonly MySQL $db,
    ) {
    }

    public function findDailyAverages(LocationFilter $filter, DateRange $range): array
    {
        return array_map(DailyWeather::fromRow(...), $this->db->queryAssoc(
            "SELECT
                DATE(mx.date) AS date,
                {$this->average("mn.min_temp")} AS minTemp,
                {$this->average("mx.max_temp")} AS maxTemp,
                {$this->average("pr.precipitation")} AS precipitation
            FROM weather.cities c
            JOIN weather.maximum_temperatures mx ON mx.city_id = c.id
            JOIN weather.minimum_temperatures mn ON mn.city_id = c.id AND mn.date = mx.date
            JOIN weather.precipitation pr ON pr.city_id = c.id AND pr.date = mx.date
            WHERE mx.date BETWEEN :fromDate AND :toDate AND {$filter->condition("c")}
            GROUP BY mx.date
            ORDER BY mx.date",
            [
                ":fromDate" => $range->fromKey(),
                ":toDate" => $range->toKey(),
                ...$filter->params(),
            ],
        ));
    }

    private function average(string $column): string
    {
        return "ROUND(AVG(NULLIF({$column}, " . self::MISSING_VALUE . ")), 1)";
    }
}
