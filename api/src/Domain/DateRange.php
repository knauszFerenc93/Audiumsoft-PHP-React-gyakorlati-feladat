<?php

declare(strict_types=1);

namespace ADS\TakeHome\Domain;

use ADS\TakeHome\Exceptions\InvalidInputException;
use DateTimeImmutable;
use JsonSerializable;

final class DateRange implements JsonSerializable
{
    private const MAX_DAYS = 366;
    private const ISO_FORMAT = "Y-m-d";
    private const KEY_FORMAT = "Ymd";

    private function __construct(
        private readonly DateTimeImmutable $from,
        private readonly DateTimeImmutable $to,
    ) {
    }

    public static function fromStrings(string $from, string $to): self
    {
        $range = new self(self::parse($from), self::parse($to));
        $range->assertOrdered();
        $range->assertWithinLimit();
        return $range;
    }

    public function fromKey(): int
    {
        return (int) $this->from->format(self::KEY_FORMAT);
    }

    public function toKey(): int
    {
        return (int) $this->to->format(self::KEY_FORMAT);
    }

    public function dayCount(): int
    {
        return (int) $this->from->diff($this->to)->days + 1;
    }

    /**
     * @return array{from: string, to: string}
     */
    public function jsonSerialize(): array
    {
        return [
            "from" => $this->from->format(self::ISO_FORMAT),
            "to" => $this->to->format(self::ISO_FORMAT),
        ];
    }

    private static function parse(string $value): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat("!" . self::ISO_FORMAT, $value);
        if ($date === false || $date->format(self::ISO_FORMAT) !== $value) {
            throw new InvalidInputException("Érvénytelen dátum: '{$value}' (várt formátum: ÉÉÉÉ-HH-NN)");
        }
        return $date;
    }

    private function assertOrdered(): void
    {
        if ($this->from > $this->to) {
            throw new InvalidInputException("A kezdődátum nem lehet későbbi a végdátumnál");
        }
    }

    private function assertWithinLimit(): void
    {
        if ($this->dayCount() > self::MAX_DAYS) {
            throw new InvalidInputException("Az időszak legfeljebb " . self::MAX_DAYS . " nap lehet");
        }
    }
}
