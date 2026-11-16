<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Controllers;

use ADS\TakeHome\Controllers\WeatherController;
use ADS\TakeHome\Domain\City;
use ADS\TakeHome\Domain\DailyWeather;
use ADS\TakeHome\Exceptions\InvalidInputException;
use ADS\TakeHome\Exceptions\NotFoundException;
use ADS\TakeHome\Filters\CityFilter;
use ADS\TakeHome\Filters\RegionFilter;
use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\Repositories\LocationRepositoryInterface;
use ADS\TakeHome\Repositories\WeatherRepositoryInterface;
use ADS\TakeHome\Units\MetricSystem;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WeatherControllerTest extends TestCase
{
    private const PERIOD = ["from" => "2023-01-01", "to" => "2023-01-01"];

    #[Test]
    public function returnsCityReport(): void
    {
        $response = $this->controller([new City(1, "Budapest", "Közép-Magyarország")])
            ->cityWeather(new HttpRequest(["cityId" => "1", ...self::PERIOD]));

        $this->assertSame(200, $response->getStatus());
        $this->assertSame(
            [
                "cities" => [["id" => 1, "name" => "Budapest", "region" => "Közép-Magyarország"]],
                "from" => "2023-01-01",
                "to" => "2023-01-01",
                "unitSystems" => [
                    "metric" => [
                        "units" => ["temperature" => "°C", "precipitation" => "mm"],
                        "days" => [["date" => "2023-01-01", "minTemp" => 1, "maxTemp" => 3, "avgTemp" => 2, "precipitation" => 0.5]],
                        "statistics" => [
                            "median" => ["minTemp" => [1], "maxTemp" => [3], "avgTemp" => [2], "precipitation" => [0.5]],
                            "mode" => ["minTemp" => [], "maxTemp" => [], "avgTemp" => [], "precipitation" => []],
                        ],
                    ],
                ],
            ],
            json_decode((string) $response->getBody(), true),
        );
    }

    #[Test]
    public function passesCityFilterToRepositories(): void
    {
        $locations = $this->createMock(LocationRepositoryInterface::class);
        $locations->expects($this->once())
            ->method("findCities")
            ->with($this->equalTo(new CityFilter(5)))
            ->willReturn([new City(5, "Nyíregyháza", "Észak és Alföld")]);
        $weather = $this->createMock(WeatherRepositoryInterface::class);
        $weather->expects($this->once())
            ->method("findDailyAverages")
            ->with($this->equalTo(new CityFilter(5)))
            ->willReturn([]);

        (new WeatherController($locations, $weather, [new MetricSystem()]))->cityWeather(new HttpRequest(["cityId" => "5", ...self::PERIOD]));
    }

    #[Test]
    public function passesRegionFilterToRepositories(): void
    {
        $locations = $this->createMock(LocationRepositoryInterface::class);
        $locations->expects($this->once())
            ->method("findCities")
            ->with($this->equalTo(new RegionFilter("Dunántúl")))
            ->willReturn([new City(3, "Keszthely", "Dunántúl")]);
        $weather = $this->createMock(WeatherRepositoryInterface::class);
        $weather->expects($this->once())
            ->method("findDailyAverages")
            ->with($this->equalTo(new RegionFilter("Dunántúl")))
            ->willReturn([]);

        (new WeatherController($locations, $weather, [new MetricSystem()]))->regionWeather(new HttpRequest(["region" => "Dunántúl", ...self::PERIOD]));
    }

    #[Test]
    public function throwsNotFoundForUnknownLocation(): void
    {
        $this->expectException(NotFoundException::class);

        $this->controller([])->regionWeather(new HttpRequest(["region" => "Atlantisz", ...self::PERIOD]));
    }

    #[Test]
    public function throwsInvalidInputForMissingDate(): void
    {
        $this->expectException(InvalidInputException::class);

        $this->controller([])->cityWeather(new HttpRequest(["cityId" => "1", "from" => "2023-01-01"]));
    }

    /**
     * @param list<City> $cities
     */
    private function controller(array $cities): WeatherController
    {
        $locations = $this->createStub(LocationRepositoryInterface::class);
        $locations->method("findCities")->willReturn($cities);
        $weather = $this->createStub(WeatherRepositoryInterface::class);
        $weather->method("findDailyAverages")->willReturn([new DailyWeather("2023-01-01", 1.0, 3.0, 0.5)]);
        return new WeatherController($locations, $weather, [new MetricSystem()]);
    }
}
