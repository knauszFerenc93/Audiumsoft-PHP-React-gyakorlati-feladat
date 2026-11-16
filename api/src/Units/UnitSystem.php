<?php

declare(strict_types=1);

namespace ADS\TakeHome\Units;

interface UnitSystem
{
    public function id(): string;

    /**
     * @return array{temperature: string, precipitation: string}
     */
    public function units(): array;

    public function temperature(float $celsius): float;

    public function precipitation(float $millimeters): float;
}
