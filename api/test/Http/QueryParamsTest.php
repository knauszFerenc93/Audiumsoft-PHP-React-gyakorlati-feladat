<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Http;

use ADS\TakeHome\Exceptions\InvalidInputException;
use ADS\TakeHome\Framework\HttpRequest;
use ADS\TakeHome\Http\QueryParams;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class QueryParamsTest extends TestCase
{
    #[Test]
    public function returnsStringValue(): void
    {
        $this->assertSame("Dunántúl", (new QueryParams(new HttpRequest(["region" => "Dunántúl"])))->string("region"));
    }

    #[Test]
    public function returnsPositiveInteger(): void
    {
        $this->assertSame(7, (new QueryParams(new HttpRequest(["cityId" => "7"])))->positiveInt("cityId"));
    }

    /**
     * @return iterable<string, array{array<string, string|list<string>>}>
     */
    public static function invalidStrings(): iterable
    {
        yield "hiányzik" => [[]];
        yield "üres" => [["value" => ""]];
        yield "tömb" => [["value" => ["a", "b"]]];
    }

    /**
     * @param array<string, string|list<string>> $query
     */
    #[Test]
    #[DataProvider("invalidStrings")]
    public function rejectsInvalidString(array $query): void
    {
        $this->expectException(InvalidInputException::class);

        (new QueryParams(new HttpRequest($query)))->string("value");
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidIntegers(): iterable
    {
        yield "szöveg" => ["abc"];
        yield "nulla" => ["0"];
        yield "negatív" => ["-3"];
        yield "tört" => ["1.5"];
    }

    #[Test]
    #[DataProvider("invalidIntegers")]
    public function rejectsInvalidInteger(string $value): void
    {
        $this->expectException(InvalidInputException::class);

        (new QueryParams(new HttpRequest(["value" => $value])))->positiveInt("value");
    }
}
