<?php

declare(strict_types=1);

namespace ADS\TakeHome\Domain;

enum Statistic: string
{
    case Median = "median";
    case Mode = "mode";

    /**
     * @param list<float> $values
     *
     * @return list<float>
     */
    public function compute(array $values): array
    {
        return match ($this) {
            self::Median => self::median($values),
            self::Mode => self::modes($values),
        };
    }

    /**
     * @param list<float> $values
     *
     * @return list<float>
     */
    private static function median(array $values): array
    {
        if ($values === []) {
            return [];
        }
        sort($values);
        $middle = intdiv(count($values), 2);
        return [count($values) % 2 === 1 ? $values[$middle] : round(($values[$middle - 1] + $values[$middle]) / 2, 3)];
    }

    /**
     * @param list<float> $values
     *
     * @return list<float>
     */
    private static function modes(array $values): array
    {
        $counts = array_count_values(array_map(strval(...), $values));
        $highest = max([0, ...array_values($counts)]);
        if ($highest < 2) {
            return [];
        }
        $modes = array_map(floatval(...), array_keys($counts, $highest, true));
        sort($modes);
        return $modes;
    }
}
