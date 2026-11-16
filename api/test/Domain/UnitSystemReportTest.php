<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Domain;

use ADS\TakeHome\Domain\DailyWeather;
use ADS\TakeHome\Domain\UnitSystemReport;
use ADS\TakeHome\Units\ImperialSystem;
use ADS\TakeHome\Units\MetricSystem;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UnitSystemReportTest extends TestCase
{
    /**
     * @return list<DailyWeather>
     */
    private static function days(): array
    {
        return [
            new DailyWeather("2023-01-01", 1.0, 3.0, 0.0),
            new DailyWeather("2023-01-02", 1.0, 5.0, null),
            new DailyWeather("2023-01-03", null, 7.0, 2.0),
        ];
    }

    #[Test]
    public function buildsMetricRowsAndStatistics(): void
    {
        $this->assertSame(
            [
                "units" => ["temperature" => "°C", "precipitation" => "mm"],
                "days" => [
                    ["date" => "2023-01-01", "minTemp" => 1.0, "maxTemp" => 3.0, "avgTemp" => 2.0, "precipitation" => 0.0],
                    ["date" => "2023-01-02", "minTemp" => 1.0, "maxTemp" => 5.0, "avgTemp" => 3.0, "precipitation" => null],
                    ["date" => "2023-01-03", "minTemp" => null, "maxTemp" => 7.0, "avgTemp" => null, "precipitation" => 2.0],
                ],
                "statistics" => [
                    "median" => ["minTemp" => [1.0], "maxTemp" => [5.0], "avgTemp" => [2.5], "precipitation" => [1.0]],
                    "mode" => ["minTemp" => [1.0], "maxTemp" => [], "avgTemp" => [], "precipitation" => []],
                ],
            ],
            (new UnitSystemReport(new MetricSystem(), self::days()))->jsonSerialize(),
        );
    }

    #[Test]
    public function computesStatisticsOnConvertedValues(): void
    {
        $report = (new UnitSystemReport(new ImperialSystem(), self::days()))->jsonSerialize();

        $this->assertSame(
            ["minTemp" => [33.8], "maxTemp" => [41.0], "avgTemp" => [36.5], "precipitation" => [0.04]],
            $report["statistics"]["median"] ?? null,
        );
    }
}
