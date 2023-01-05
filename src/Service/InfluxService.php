<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\InfluxConfig;
use GuzzleHttp\Client;

final class InfluxService
{
    private Client $client;

    public function __construct(private readonly InfluxConfig $influxConfig)
    {
        $this->client = new Client();
    }

    public function push(string $channel, int $value): void
    {
        $body = sprintf(
            '%s,item=%s value=%d',
            $this->influxConfig->db,
            $channel,
            $value
        );

        $this->client->post(
            $this->influxConfig->url,
            [
                'body' => $body,
            ]
        );
    }
}
