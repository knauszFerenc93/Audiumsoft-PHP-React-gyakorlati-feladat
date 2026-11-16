<?php

declare(strict_types=1);

namespace ADS\TakeHome\Domain;

final class DailyWeather
{
    public function __construct(
        public readonly string $date,
        public readonly ?float $minTemp,
        public readonly ?float $maxTemp,
        public readonly ?float $precipitation,
    ) {
    }

    /**
     * @param array<string, scalar|null> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            (string) $row["date"],
            self::nullableFloat($row["minTemp"]),
            self::nullableFloat($row["maxTemp"]),
            self::nullableFloat($row["precipitation"]),
        );
    }

    public function avgTemp(): ?float
    {
        if ($this->minTemp === null || $this->maxTemp === null) {
            return null;
        }
        return round(($this->minTemp + $this->maxTemp) / 2, 1);
    }

    private static function nullableFloat(string|int|float|bool|null $value): ?float
    {
        return $value === null ? null : (float) $value;
    }
}
