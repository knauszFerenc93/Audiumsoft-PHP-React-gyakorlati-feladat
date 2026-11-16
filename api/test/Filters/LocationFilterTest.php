<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Filters;

use ADS\TakeHome\Filters\AllCitiesFilter;
use ADS\TakeHome\Filters\CityFilter;
use ADS\TakeHome\Filters\RegionFilter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LocationFilterTest extends TestCase
{
    #[Test]
    public function cityFilterMatchesById(): void
    {
        $filter = new CityFilter(3);

        $this->assertSame("c.id = :cityId", $filter->condition("c"));
        $this->assertSame([":cityId" => 3], $filter->params());
    }

    #[Test]
    public function regionFilterMatchesByRegion(): void
    {
        $filter = new RegionFilter("Dunántúl");

        $this->assertSame("x.region = :region", $filter->condition("x"));
        $this->assertSame([":region" => "Dunántúl"], $filter->params());
    }

    #[Test]
    public function allCitiesFilterMatchesEverything(): void
    {
        $filter = new AllCitiesFilter();

        $this->assertSame("TRUE", $filter->condition("c"));
        $this->assertSame([], $filter->params());
    }
}
