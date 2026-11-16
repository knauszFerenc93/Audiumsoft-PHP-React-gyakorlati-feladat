<?php

declare(strict_types=1);

namespace ADS\TakeHome\Domain;

use ADS\TakeHome\Units\UnitSystem;

enum WeatherColumn: string
{
    case MinTemp = "minTemp";
    case MaxTemp = "maxTemp";
    case AvgTemp = "avgTemp";
    case Precipitation = "precipitation";

    public function measure(DailyWeather $day, UnitSystem $units): ?float
    {
        $value = $this->rawValue($day);
        return $value === null ? null : $this->convert($value, $units);
    }

    private function rawValue(DailyWeather $day): ?float
    {
        return match ($this) {
            self::MinTemp => $day->minTemp,
            self::MaxTemp => $day->maxTemp,
            self::AvgTemp => $day->avgTemp(),
            self::Precipitation => $day->precipitation,
        };
    }

    private function convert(float $value, UnitSystem $units): float
    {
        return $this === self::Precipitation ? $units->precipitation($value) : $units->temperature($value);
    }
}
