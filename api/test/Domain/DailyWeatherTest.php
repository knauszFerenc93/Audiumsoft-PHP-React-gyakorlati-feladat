<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Domain;

use ADS\TakeHome\Domain\DailyWeather;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DailyWeatherTest extends TestCase
{
    #[Test]
    public function calculatesAverageFromMinimumAndMaximum(): void
    {
        $this->assertSame(6.0, (new DailyWeather("2023-01-01", 3.4, 8.5, 0.0))->avgTemp());
        $this->assertSame(-0.6, (new DailyWeather("2023-01-01", -1.3, 0.1, 0.0))->avgTemp());
    }

    #[Test]
    public function averageIsNullWhenAnyTemperatureIsMissing(): void
    {
        $this->assertNull((new DailyWeather("2023-01-01", null, 8.5, 0.0))->avgTemp());
        $this->assertNull((new DailyWeather("2023-01-01", 3.4, null, 0.0))->avgTemp());
    }

    #[Test]
    public function buildsFromDatabaseRow(): void
    {
        $this->assertEquals(
            new DailyWeather("2023-01-01", -1.5, 4.0, null),
            DailyWeather::fromRow(["date" => "2023-01-01", "minTemp" => "-1.5", "maxTemp" => 4, "precipitation" => null]),
        );
    }
}
