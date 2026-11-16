<?php

declare(strict_types=1);

namespace ADS\TakeHome\Domain;

use ADS\TakeHome\Units\UnitSystem;
use JsonSerializable;

final class UnitSystemReport implements JsonSerializable
{
    /**
     * @param list<DailyWeather> $days
     */
    public function __construct(
        private readonly UnitSystem $units,
        private readonly array $days,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            "units" => $this->units->units(),
            "days" => array_map($this->row(...), $this->days),
            "statistics" => $this->statistics(),
        ];
    }

    /**
     * @return array<string, float|string|null>
     */
    private function row(DailyWeather $day): array
    {
        return ["date" => $day->date, ...$this->mapColumns(fn (WeatherColumn $column) => $column->measure($day, $this->units))];
    }

    /**
     * @return array<string, array<string, list<float>>>
     */
    private function statistics(): array
    {
        return array_combine(
            array_map(static fn (Statistic $statistic): string => $statistic->value, Statistic::cases()),
            array_map(
                fn (Statistic $statistic): array => $this->mapColumns(fn (WeatherColumn $column) => $statistic->compute($this->columnValues($column))),
                Statistic::cases(),
            ),
        );
    }

    /**
     * @return list<float>
     */
    private function columnValues(WeatherColumn $column): array
    {
        return array_values(array_filter(
            array_map(fn (DailyWeather $day): ?float => $column->measure($day, $this->units), $this->days),
            static fn (?float $value): bool => $value !== null,
        ));
    }

    /**
     * @template T
     *
     * @param callable(WeatherColumn): T $map
     *
     * @return array<string, T>
     */
    private function mapColumns(callable $map): array
    {
        return array_combine(
            array_map(static fn (WeatherColumn $column): string => $column->value, WeatherColumn::cases()),
            array_map($map, WeatherColumn::cases()),
        );
    }
}
