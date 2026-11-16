<?php

declare(strict_types=1);

namespace ADS\TakeHome\Controllers;

use ADS\TakeHome\Domain\City;
use ADS\TakeHome\Domain\DateRange;
use ADS\TakeHome\Domain\WeatherReport;
use ADS\TakeHome\Exceptions\NotFoundException;
use ADS\TakeHome\Filters\CityFilter;
use ADS\TakeHome\Filters\LocationFilter;
use ADS\TakeHome\Filters\RegionFilter;
use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\Framework\HttpResponseInterface;
use ADS\TakeHome\Framework\JsonHttpResponse;
use ADS\TakeHome\Http\QueryParams;
use ADS\TakeHome\Repositories\LocationRepositoryInterface;
use ADS\TakeHome\Repositories\WeatherRepositoryInterface;
use ADS\TakeHome\Units\UnitSystem;

final class WeatherController
{
    /**
     * @param list<UnitSystem> $unitSystems
     */
    public function __construct(
        private readonly LocationRepositoryInterface $locations,
        private readonly WeatherRepositoryInterface $weather,
        private readonly array $unitSystems,
    ) {
    }

    public function cityWeather(HttpRequest $request): HttpResponseInterface
    {
        $params = new QueryParams($request);
        return $this->report(new CityFilter($params->positiveInt("cityId")), $params);
    }

    public function regionWeather(HttpRequest $request): HttpResponseInterface
    {
        $params = new QueryParams($request);
        return $this->report(new RegionFilter($params->string("region")), $params);
    }

    private function report(LocationFilter $filter, QueryParams $params): HttpResponseInterface
    {
        $range = DateRange::fromStrings($params->string("from"), $params->string("to"));
        return new JsonHttpResponse(new WeatherReport(
            $this->existingCities($filter),
            $range,
            $this->weather->findDailyAverages($filter, $range),
            $this->unitSystems,
        ));
    }

    /**
     * @return list<City>
     */
    private function existingCities(LocationFilter $filter): array
    {
        $cities = $this->locations->findCities($filter);
        if ($cities === []) {
            throw new NotFoundException("Nem található: {$filter->describe()}");
        }
        return $cities;
    }
}
