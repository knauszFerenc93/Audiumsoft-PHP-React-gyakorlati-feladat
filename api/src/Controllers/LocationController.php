<?php

declare(strict_types=1);

namespace ADS\TakeHome\Controllers;

use ADS\TakeHome\Filters\AllCitiesFilter;
use ADS\TakeHome\Framework\HttpResponseInterface;
use ADS\TakeHome\Framework\JsonHttpResponse;
use ADS\TakeHome\Repositories\LocationRepositoryInterface;

final class LocationController
{
    public function __construct(
        private readonly LocationRepositoryInterface $locations,
    ) {
    }

    public function listCities(): HttpResponseInterface
    {
        return new JsonHttpResponse($this->locations->findCities(new AllCitiesFilter()));
    }

    public function listRegions(): HttpResponseInterface
    {
        return new JsonHttpResponse($this->locations->findRegions());
    }
}
