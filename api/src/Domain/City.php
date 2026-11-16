<?php

declare(strict_types=1);

namespace ADS\TakeHome\Domain;

use JsonSerializable;

final class City implements JsonSerializable
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $region,
    ) {
    }

    /**
     * @param array<string, scalar|null> $row
     */
    public static function fromRow(array $row): self
    {
        return new self((int) $row["id"], (string) $row["name"], (string) $row["region"]);
    }

    /**
     * @return array{id: int, name: string, region: string}
     */
    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "region" => $this->region,
        ];
    }
}
