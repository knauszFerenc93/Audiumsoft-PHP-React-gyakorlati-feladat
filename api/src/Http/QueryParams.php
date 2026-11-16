<?php

declare(strict_types=1);

namespace ADS\TakeHome\Http;

use ADS\TakeHome\Exceptions\InvalidInputException;
use ADS\TakeHome\Framework\HttpRequest;

final class QueryParams
{
    public function __construct(
        private readonly HttpRequest $request,
    ) {
    }

    public function string(string $name): string
    {
        $value = $this->request->query[$name] ?? null;
        if (!is_string($value) || $value === "") {
            throw new InvalidInputException("'{$name}' query paraméter kötelező");
        }
        return $value;
    }

    public function positiveInt(string $name): int
    {
        $value = filter_var($this->string($name), FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if ($value === false) {
            throw new InvalidInputException("'{$name}' query paraméter pozitív egész szám kell legyen");
        }
        return $value;
    }
}
