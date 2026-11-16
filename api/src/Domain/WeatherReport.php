<?php

declare(strict_types=1);

namespace ADS\TakeHome\Domain;

use ADS\TakeHome\Units\UnitSystem;
use JsonSerializable;

final class WeatherReport implements JsonSerializable
{
    /**
     * @param list<City> $cities
     * @param list<DailyWeather> $days
     * @param list<UnitSystem> $unitSystems
     */
    public function __construct(
        private readonly array $cities,
        private readonly DateRange $range,
        private readonly array $days,
        private readonly array $unitSystems,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            "cities" => $this->cities,
            ...$this->range->jsonSerialize(),
            "unitSystems" => array_combine(
                array_map(static fn (UnitSystem $units): string => $units->id(), $this->unitSystems),
                array_map(fn (UnitSystem $units): UnitSystemReport => new UnitSystemReport($units, $this->days), $this->unitSystems),
            ),
        ];
    }
}
