<?php

declare(strict_types=1);

namespace App\Model;

use Padhie\TwitchApiBundle\Response\Streams\Stream;

final class TwitchStreamCollection
{
    /** @var array<string, Stream> */
    private array $streams = [];

    /**
     * @param array<int, Stream> $streams
     */
    public function __construct(array $streams = [])
    {
        foreach ($streams as $stream) {
            $this->add($stream);
        }
    }

    public function add(Stream $stream): void
    {
        $lowerChannelName = strtolower($stream->getUserLogin());
        $this->streams[$lowerChannelName] = $stream;
    }

    public function get(string $name): ?Stream
    {
        $lowerChannelName = strtolower($name);

        return $this->streams[$lowerChannelName] ?? null;
    }

    /**
     * @return array<int, string>
     */
    public function getAllNames(): array
    {
        return array_map(
            static fn(Stream $stream): string => $stream->getUserLogin(),
            $this->streams
        );
    }

    public function count(): int
    {
        return count($this->streams);
    }
}
