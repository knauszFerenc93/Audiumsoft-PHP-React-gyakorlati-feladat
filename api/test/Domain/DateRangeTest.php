<?php

declare(strict_types=1);

namespace ADS\TakeHome\Tests\Domain;

use ADS\TakeHome\Domain\DateRange;
use ADS\TakeHome\Exceptions\InvalidInputException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateRangeTest extends TestCase
{
    #[Test]
    public function convertsToDatabaseKeys(): void
    {
        $range = DateRange::fromStrings("2023-01-05", "2023-02-10");

        $this->assertSame(20230105, $range->fromKey());
        $this->assertSame(20230210, $range->toKey());
    }

    #[Test]
    public function serializesToIsoDates(): void
    {
        $this->assertSame(
            ["from" => "2023-01-05", "to" => "2023-02-10"],
            DateRange::fromStrings("2023-01-05", "2023-02-10")->jsonSerialize(),
        );
    }

    #[Test]
    public function countsDaysInclusively(): void
    {
        $this->assertSame(1, DateRange::fromStrings("2023-01-01", "2023-01-01")->dayCount());
        $this->assertSame(366, DateRange::fromStrings("2024-01-01", "2024-12-31")->dayCount());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalidRanges(): iterable
    {
        yield "nem létező nap" => ["2023-02-30", "2023-03-01"];
        yield "rossz formátum" => ["2023/01/01", "2023-01-02"];
        yield "időpont is" => ["2023-01-01 10:00", "2023-01-02"];
        yield "szöveg" => ["tegnap", "2023-01-02"];
        yield "fordított sorrend" => ["2023-03-01", "2023-01-01"];
        yield "túl hosszú" => ["2023-01-01", "2024-01-02"];
    }

    #[Test]
    #[DataProvider("invalidRanges")]
    public function rejectsInvalidInput(string $from, string $to): void
    {
        $this->expectException(InvalidInputException::class);

        DateRange::fromStrings($from, $to);
    }
}
