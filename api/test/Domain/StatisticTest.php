<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Domain;

use ADS\TakeHome\Domain\Statistic;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StatisticTest extends TestCase
{
    /**
     * @return iterable<string, array{Statistic, list<float>, list<float>}>
     */
    public static function cases(): iterable
    {
        yield "medián páratlan" => [Statistic::Median, [3.0, 1.0, 2.0], [2.0]];
        yield "medián páros" => [Statistic::Median, [4.0, 1.0, 3.0, 2.0], [2.5]];
        yield "medián üres" => [Statistic::Median, [], []];
        yield "medián kerekítés" => [Statistic::Median, [0.1, 0.2], [0.15]];
        yield "módusz egy" => [Statistic::Mode, [1.5, 2.0, 1.5], [1.5]];
        yield "módusz több" => [Statistic::Mode, [3.0, 1.0, 3.0, 1.0, 2.0], [1.0, 3.0]];
        yield "módusz nincs ismétlődés" => [Statistic::Mode, [1.0, 2.0, 3.0], []];
        yield "módusz üres" => [Statistic::Mode, [], []];
        yield "módusz negatív és nulla" => [Statistic::Mode, [-0.5, 0.0, -0.5, 0.0, 0.0], [0.0]];
    }

    /**
     * @param list<float> $values
     * @param list<float> $expected
     */
    #[Test]
    #[DataProvider("cases")]
    public function computes(Statistic $statistic, array $values, array $expected): void
    {
        $this->assertSame($expected, $statistic->compute($values));
    }
}
