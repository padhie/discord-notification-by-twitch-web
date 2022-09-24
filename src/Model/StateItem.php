<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class StateItem implements JsonSerializable
{
    public string $name;
    public bool $online;

    private function __construct(string $name, bool $online)
    {
        $this->name = $name;
        $this->online = $online;
    }

    /** @param array<mixed> $data */
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['online'] ?? false
        );
    }

    /** @return array<mixed> */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'online' => $this->online,
        ];
    }
}
