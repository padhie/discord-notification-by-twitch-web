<?php

declare(strict_types=1);

namespace App\Model;

final class TwitchUserChannelCollection
{
    /** @var array<string, TwitchUserChannel> */
    private array $streams = [];

    /**
     * @param array<int, TwitchUserChannel> $streams
     */
    public function __construct(array $streams = [])
    {
        foreach ($streams as $stream) {
            $this->add($stream);
        }
    }

    public function add(TwitchUserChannel $stream): void
    {
        $lowerChannelName = strtolower($stream->getUser()->getDisplayName());
        $this->streams[$lowerChannelName] = $stream;
    }

    public function get(string $name): ?TwitchUserChannel
    {
        $lowerChannelName = strtolower($name);

        return $this->streams[$lowerChannelName] ?? null;
    }

    public function count(): int
    {
        return count($this->streams);
    }
}
