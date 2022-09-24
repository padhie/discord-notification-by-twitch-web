<?php

namespace App\Model;

use JsonSerializable;

final class Output implements JsonSerializable
{
    private TwitchConfig $twitchConfig;
    private InfluxConfig $influxConfig;
    /** @var StreamerItem[] */
    private array $streamers;

    /** @param StreamerItem[] $streamers */
    public function __construct(TwitchConfig $twitchConfig, InfluxConfig $influxConfig, array $streamers)
    {
        $this->twitchConfig = $twitchConfig;
        $this->influxConfig = $influxConfig;
        $this->streamers = $streamers;
    }

    /** @return array<mixed> */
    public function jsonSerialize(): array
    {
        return [
            'twitch' => $this->twitchConfig,
            'influx' => $this->influxConfig,
            'streamer' => $this->streamers,
        ];
    }
}
