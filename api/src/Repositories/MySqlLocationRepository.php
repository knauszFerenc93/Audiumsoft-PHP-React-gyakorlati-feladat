<?php

declare(strict_types=1);

namespace ADS\TakeHome\Repositories;

use ADS\TakeHome\Domain\City;
use ADS\TakeHome\Filters\LocationFilter;
use ADS\TakeHome\Framework\MySQL;

final class MySqlLocationRepository implements LocationRepositoryInterface
{
    public function __construct(
        private readonly MySQL $db,
    ) {
    }

    public function findCities(LocationFilter $filter): array
    {
        return array_map(City::fromRow(...), $this->db->queryAssoc(
            "SELECT c.id, c.name, c.region
            FROM weather.cities c
            WHERE {$filter->condition("c")}
            ORDER BY c.name, c.id",
            $filter->params(),
        ));
    }

    public function findRegions(): array
    {
        return array_map(
            static fn (array $row): string => (string) $row["region"],
            $this->db->queryAssoc("SELECT DISTINCT region FROM weather.cities ORDER BY region"),
        );
    }
}
