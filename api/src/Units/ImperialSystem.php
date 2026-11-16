<?php

declare(strict_types=1);

namespace ADS\TakeHome\Units;

final class ImperialSystem implements UnitSystem
{
    private const MILLIMETERS_PER_INCH = 25.4;

    public function id(): string
    {
        return "imperial";
    }

    public function units(): array
    {
        return ["temperature" => "°F", "precipitation" => "in"];
    }

    public function temperature(float $celsius): float
    {
        return round($celsius * 9 / 5 + 32, 1);
    }

    public function precipitation(float $millimeters): float
    {
        return round($millimeters / self::MILLIMETERS_PER_INCH, 2);
    }
}
