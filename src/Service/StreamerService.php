<?php

namespace App\Service;

use App\Model\Streamer;
use App\Model\StreamerItem;
use JsonException;

final class StreamerService
{
    private const STREAMER_FILE = 'data/streamer.json';

    public function __construct(private readonly string $baseDirectory)
    {
    }

    public function getRawStreamerContent(): string
    {
        $content = file_get_contents($this->getFullFilePath());

        return $content !== false
            ? $content
            : '';
    }

    public function saveRawStreamerContent(string $content): void
    {
        file_put_contents($this->getFullFilePath(), $content);
    }

    /**
     * @throws JsonException
     */
    public function getCurrentStreamer(): Streamer
    {
        $data = json_decode(
            $this->getRawStreamerContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return Streamer::createFromArray($data);
    }

    /**
     * @throws JsonException
     */
    public function saveStreamer(Streamer $streamer): void
    {
        $content = json_encode($streamer, JSON_THROW_ON_ERROR);
        $this->saveRawStreamerContent($content);
    }

    /**
     * @throws JsonException
     */
    public function getSingleStreamerItem(string $channel): ?StreamerItem
    {
        $streamer = $this->getCurrentStreamer();

        foreach ($streamer->items as $items) {
            foreach ($items as $item) {
                if ($item->channel === $channel) {
                    return $item;
                }
            }
        }

        return null;
    }

    private function getFullFilePath(): string
    {
        return $this->baseDirectory . DIRECTORY_SEPARATOR . self::STREAMER_FILE;
    }
}
