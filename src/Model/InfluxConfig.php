<?php

namespace App\Model;

use JsonSerializable;

final class InfluxConfig implements JsonSerializable
{
    public string $url;
    public string $db;

    public function __construct(string $url, string $db)
    {
        $this->url = $url;
        $this->db = $db;
    }

    /** @return array<mixed> */
    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'db' => $this->db,
        ];
    }
}
