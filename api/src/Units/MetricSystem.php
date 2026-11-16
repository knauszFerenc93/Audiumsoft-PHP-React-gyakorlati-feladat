<?php

declare(strict_types=1);

namespace ADS\TakeHome\Units;

final class MetricSystem implements UnitSystem
{
    public function id(): string
    {
        return "metric";
    }

    public function units(): array
    {
        return ["temperature" => "°C", "precipitation" => "mm"];
    }

    public function temperature(float $celsius): float
    {
        return round($celsius, 1);
    }

    public function precipitation(float $millimeters): float
    {
        return round($millimeters, 1);
    }
}
