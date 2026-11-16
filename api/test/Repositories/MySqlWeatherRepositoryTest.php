<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Repositories;

use ADS\TakeHome\Domain\DailyWeather;
use ADS\TakeHome\Domain\DateRange;
use ADS\TakeHome\Filters\RegionFilter;
use ADS\TakeHome\Framework\MySQL;
use ADS\TakeHome\Repositories\MySqlWeatherRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MySqlWeatherRepositoryTest extends TestCase
{
    #[Test]
    public function queriesWithStringParameterLastAndMapsRows(): void
    {
        $db = $this->createMock(MySQL::class);
        $db->expects($this->once())
            ->method("queryAssoc")
            ->with(
                $this->stringContains("c.region = :region"),
                $this->identicalTo([":fromDate" => 20230101, ":toDate" => 20230102, ":region" => "Dunántúl"]),
            )
            ->willReturn([["date" => "2023-01-01", "minTemp" => 1.0, "maxTemp" => 3.0, "precipitation" => null]]);

        $this->assertEquals(
            [new DailyWeather("2023-01-01", 1.0, 3.0, null)],
            (new MySqlWeatherRepository($db))->findDailyAverages(
                new RegionFilter("Dunántúl"),
                DateRange::fromStrings("2023-01-01", "2023-01-02"),
            ),
        );
    }

    #[Test]
    public function excludesMissingValuesFromAverages(): void
    {
        $db = $this->createMock(MySQL::class);
        $db->expects($this->once())
            ->method("queryAssoc")
            ->with($this->stringContains("ROUND(AVG(NULLIF(mx.max_temp, -999)), 1)"), $this->anything())
            ->willReturn([]);

        (new MySqlWeatherRepository($db))->findDailyAverages(
            new RegionFilter("Dunántúl"),
            DateRange::fromStrings("2023-01-01", "2023-01-02"),
        );
    }
}
