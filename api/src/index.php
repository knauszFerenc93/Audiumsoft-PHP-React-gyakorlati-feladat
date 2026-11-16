<?php

namespace ADS\TakeHome;

include __DIR__ . "/../vendor/autoload.php";

use ADS\TakeHome\Controllers\ExampleController;
use ADS\TakeHome\Controllers\LocationController;
use ADS\TakeHome\Controllers\WeatherController;
use ADS\TakeHome\Framework\MySQL;
use ADS\TakeHome\Framework\Router;
use ADS\TakeHome\Http\HttpErrorHandler;
use ADS\TakeHome\Repositories\MySqlLocationRepository;
use ADS\TakeHome\Repositories\MySqlWeatherRepository;
use ADS\TakeHome\Units\ImperialSystem;
use ADS\TakeHome\Units\MetricSystem;

$router = new Router();

$mysql = new MySQL("db", "weather", "root", "root");
$helloController = new ExampleController(greeting: "Hello", db: $mysql);
$locationRepository = new MySqlLocationRepository($mysql);
$locationController = new LocationController($locationRepository);
$weatherController = new WeatherController(
    $locationRepository,
    new MySqlWeatherRepository($mysql),
    [new MetricSystem(), new ImperialSystem()],
);

foreach (
    [
        "/hello" => $helloController->sayHello(...),
        "/budapest" => $helloController->getBudapestMaxTempCount(...),
        "/cities" => $locationController->listCities(...),
        "/regions" => $locationController->listRegions(...),
        "/weather/city" => $weatherController->cityWeather(...),
        "/weather/region" => $weatherController->regionWeather(...),
    ] as $path => $handler
) {
    $router->addRoute("GET", $path, HttpErrorHandler::wrap($handler));
}

$router->handleRequest();
