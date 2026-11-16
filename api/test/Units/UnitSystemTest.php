<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Units;

use ADS\TakeHome\Units\ImperialSystem;
use ADS\TakeHome\Units\MetricSystem;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UnitSystemTest extends TestCase
{
    #[Test]
    public function metricKeepsValues(): void
    {
        $units = new MetricSystem();

        $this->assertSame(-3.4, $units->temperature(-3.4));
        $this->assertSame(12.5, $units->precipitation(12.5));
        $this->assertSame(["temperature" => "°C", "precipitation" => "mm"], $units->units());
    }

    #[Test]
    public function imperialConvertsToFahrenheitAndInches(): void
    {
        $units = new ImperialSystem();

        $this->assertSame(32.0, $units->temperature(0.0));
        $this->assertSame(-4.0, $units->temperature(-20.0));
        $this->assertSame(98.6, $units->temperature(37.0));
        $this->assertSame(1.0, $units->precipitation(25.4));
        $this->assertSame(0.24, $units->precipitation(6.1));
        $this->assertSame(["temperature" => "°F", "precipitation" => "in"], $units->units());
    }
}
