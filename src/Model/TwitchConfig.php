<?php

namespace App\Model;

use JsonSerializable;

final class TwitchConfig implements JsonSerializable
{
    private string $url;
    private string $clientId;

    public function __construct(string $url, string $clientId)
    {
        $this->url = $url;
        $this->clientId = $clientId;
    }

    /** @return array<mixed> */
    public function jsonSerialize(): array
    {
        return [
            'url' => $this->url,
            'clientId' => $this->clientId,
        ];
    }
}
